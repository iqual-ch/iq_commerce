<?php

namespace Drupal\iq_commerce_required_product\EventSubscriber;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\commerce_cart\Event\CartOrderItemRemoveEvent;
use Drupal\commerce_cart\Event\CartOrderItemUpdateEvent;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iq_commerce\Event\IqCommerceAfterCartAddEvent;
use Drupal\iq_commerce\Event\IqCommerceBeforeCartAddEvent;
use Drupal\iq_commerce\Event\IqCommerceCartEvents;
use Drupal\iq_commerce\Form\IqCommerceProductSettingsForm;
use Drupal\iq_commerce_required_product\Form\RequiredProductSettingsForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequiredProductEventSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The cart manager.
   *
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected $cartManager;

  /**
   * Constructs a new RequiredProductEventSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   *
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager,EntityRepositoryInterface $entity_repository, CartManagerInterface $cart_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
    $this->cartManager = $cart_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      IqCommerceCartEvents::BEFORE_CART_ENTITY_ADD => 'addRequiredProducts',
      IqCommerceCartEvents::AFTER_CART_ENTITY_ADD => 'lockRequiredProducts',
      CartEvents::CART_ORDER_ITEM_UPDATE => 'updateRequiredProducts',
      CartEvents::CART_ORDER_ITEM_REMOVE => 'removeRequiredProducts'
    ];
    return $events;
  }

  /**
   * Adds order items to the cart which are required.
   *
   * @param \Drupal\iq_commerce\Event\IqCommerceBeforeCartAddEvent $event
   *   The before add to cart event.
   */
  public function addRequiredProducts(IqCommerceBeforeCartAddEvent $event) {
    $body = $event->getBody();
    $added_products = [];

    foreach ($body as $order_item_data) {
      $storage = $this->entityTypeManager->getStorage($order_item_data['purchased_entity_type']);

      $purchased_entity = $storage->load($order_item_data['purchased_entity_id']);
      if (!$purchased_entity || !$purchased_entity instanceof PurchasableEntityInterface) {
        continue;
      }
      /** @var \Drupal\commerce_product\Entity\Product $purchased_entity */
      $purchased_entity = $this->entityRepository->getTranslationFromContext($purchased_entity);

      // Get all required field references in the product from the settings.
      $iqCommerceProductSettingsConfig = RequiredProductSettingsForm::getSettings();
      $required_field_names = $iqCommerceProductSettingsConfig['required_product_fields'];

      /** If the required product is not in the cart, add it to the body. */
      foreach ($required_field_names as $required_field_name => $field_settings) {
        if ($purchased_entity->hasField($required_field_name)) {
          $required_products = $purchased_entity->get($required_field_name)->getValue();
          $required_products_reference_type = $purchased_entity->get($required_field_name)->getFieldDefinition()->get('entity_type');
        }
        elseif ($purchased_entity->getProduct()->hasField($required_field_name)){
          $required_products = $purchased_entity->getProduct()->get($required_field_name)->getValue();
          $required_products_reference_type = $purchased_entity->getProduct()->get($required_field_name)->getFieldDefinition()->get('entity_type');
        }
        if (!empty($required_products)) {
          foreach ($required_products as $required_product) {
            if (!empty($required_product['target_id'])) {
              if ($required_products_reference_type == 'commerce_product') {
                /** @var Product $required_product */
                $required_product = Product::load($required_product['target_id']);
                $requred_product_variations = $required_product->getVariations();
                $required_product = reset($requred_product_variations);
              }
              else {
                $required_product = ProductVariation::load($required_product['target_id']);
              }
              if (!empty($required_product)) {
                $added_products[] = $required_product;
                if ($field_settings['sync_quantities']) {
                  $quantity =  (!empty($order_item_data['quantity'])) ? $order_item_data['quantity'] : 1;
                }
                else {
                  /*
                   * @TODO Get the cart and iterate through the items to check
                   * whether the required product is already there and skip to
                   * next item if it is.
                  */
                  $quantity = 1;
                }
                $body[] = [
                  'purchased_entity_type' => 'commerce_product_variation',
                  'purchased_entity_id' => $required_product->id(),
                  'quantity' => $quantity,
                ];
              }
            }
          }
        }
      }
    }
    // If there were any required products that were added, add them to the
    // event's body, so they can be sent to the cart API.
    if (count($added_products) > 0) {
      $event->setAdditionalData(['required_products' => array_values($added_products)]);
      $event->setBody($body);
    }
  }

  public function lockRequiredProducts(IqCommerceAfterCartAddEvent $event) {
    \Drupal::logger('iq_commerce_required_product')->notice('entered the after acard event');
    $response_data = $event->getResponseWithAdditionalData();
    \Drupal::logger('iq_commerce_required_product')->notice(json_encode(array_keys($response_data->getResponseData())));
    $dependency_items = [];
    foreach ($response_data->getResponseData()['order_items'] as $required_item) {
      $dependency_items = array_merge($this->getRequiredVariations($required_item->getPurchasedEntity(), $required_item->getQuantity()), $dependency_items);
    }

    foreach ($response_data->getResponseData()['order_items'] as $required_item) {
      foreach($dependency_items as $dependency_item) {
        if ($required_item->getPurchasedEntity()->getSku() == $dependency_item['required_product']->getSku()) {
          $required_item->lock()->save();
        }
      }
    }

  }

  /**
   *
   */
  public function updateRequiredProducts(CartOrderItemUpdateEvent $event) {
    $order_item = $event->getOrderItem();
    /** @var ProductVariation $variation */
    $variation = $order_item->getPurchasedEntity();

    $requiredItems = $this->getRequiredVariations($variation, $order_item->getQuantity());
    $cart = $order_item->getOrder();
    foreach ($cart->getItems() as $item) {
      /**
       * @var int $key
       * @var ProductVariation $requiredItem
       */
      foreach ($requiredItems as $key => $requiredItem) {
        if (!empty($requiredItem['required_product']) && $requiredItem['required_product']->getSku() == $item->getPurchasedEntity()->getSku() && $item->getQuantity() != $requiredItem['quantity']) {
          $item->setQuantity($requiredItem['quantity']);
          $this->cartManager->updateOrderItem($cart, $item, TRUE);
        }
      }
    }
  }

  /**
   * Helper function when removing products that have dependency products.
   */
  public function removeRequiredProducts(CartOrderItemRemoveEvent $event) {
    $order_item = $event->getOrderItem();
    /** @var ProductVariation $variation */
    $variation = $order_item->getPurchasedEntity();
    $requiredItems = $this->getRequiredVariations($variation, $order_item->getQuantity());
    $cart = $order_item->getOrder();
    foreach ($cart->getItems() as $item) {
      /**
       * @var int $key
       * @var ProductVariation $requiredItem
       */
      foreach ($requiredItems as $key => $requiredItem) {
        if ($requiredItem['required_product']->getSku() == $item->getPurchasedEntity()->getSku()) {
          $item->delete();
        }
      }
    }
  }

  /**
   * Helper function to get required variations for the item.
   * @param $purchased_entity
   * @param $purchased_entity_quantity
   * @return array
   */
  private function getRequiredVariations($purchased_entity, $purchased_entity_quantity)
  {
    // Get all required field references in the product from the settings.
    $iqCommerceProductSettingsConfig = RequiredProductSettingsForm::getSettings();
    $required_field_names = $iqCommerceProductSettingsConfig['required_product_fields'];
    $added_products = [];
    $i = 0;

    /** If the required product is not in the cart, add it to the body. */
    foreach ($required_field_names as $required_field_name => $field_settings) {
      if ($purchased_entity->hasField($required_field_name)) {
        $required_products = $purchased_entity->get($required_field_name)->getValue();
        $required_products_reference_type = $purchased_entity->get($required_field_name)->getFieldDefinition()->get('entity_type');
      } elseif ($purchased_entity instanceof ProductVariation && $purchased_entity->getProduct()->hasField($required_field_name)) {
        $required_products = $purchased_entity->getProduct()->get($required_field_name)->getValue();
        $required_products_reference_type = $purchased_entity->getProduct()->get($required_field_name)->getFieldDefinition()->get('entity_type');
      }
      if (!empty($required_products)) {
        foreach ($required_products as $required_product) {
          if (!empty($required_product['target_id'])) {
            if ($required_products_reference_type == 'commerce_product') {
              /** @var Product $required_product */
              $required_product = Product::load($required_product['target_id']);
              $required_product_variations = $required_product->getVariations();
              $required_product = reset($required_product_variations);
            } else {
              $required_product = ProductVariation::load($required_product['target_id']);
            }
            if (!empty($required_product)) {
              $added_products[$i]['required_product'] = $required_product;
              if ($field_settings['sync_quantities']) {
                $quantity =  (!empty($purchased_entity_quantity)) ? $purchased_entity_quantity : 1;
              }
              else {
                /*
                 * @TODO Get the cart and iterate through the items to check
                 * whether the required product is already there and skip to
                 * next item if it is.
                */
                $quantity = 1;
              }
              $added_products[$i]['quantity'] = $quantity;
              $i++;
            }
          }
        }
      }
    }
    return $added_products;
  }
}
