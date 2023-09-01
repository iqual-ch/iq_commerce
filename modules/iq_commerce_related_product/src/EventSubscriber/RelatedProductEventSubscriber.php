<?php

namespace Drupal\iq_commerce_related_product\EventSubscriber;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iq_commerce\Event\IqCommerceAfterCartAddEvent;
use Drupal\iq_commerce\Event\IqCommerceCartEvents;
use Drupal\iq_commerce_related_product\Form\RelatedProductSettingsForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles related products on adding products to cart.
 */
class RelatedProductEventSubscriber implements EventSubscriberInterface {

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
   * Constructs a new RequiredProductEventSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      IqCommerceCartEvents::AFTER_CART_ENTITY_ADD => 'suggestRelatedProducts',
    ];
    return $events;
  }

  /**
   * Suggests order items to be added to the cart.
   *
   * @param \Drupal\iq_commerce\Event\IqCommerceAfterCartAddEvent $event
   *   The after add to cart event.
   */
  public function suggestRelatedProducts(IqCommerceAfterCartAddEvent $event) {
    $suggested_products = [];
    $response = $event->getResponse();
    $order_items = $response->getResponseData();

    // Get all related field references in the product from the settings.
    $iqCommerceProductSettingsConfig = RelatedProductSettingsForm::getSettings();
    $related_field_names = $iqCommerceProductSettingsConfig['related_product_fields'];
    /** @var \Drupal\commerce_order\Entity\OrderItem $order_item */
    foreach ($order_items as $order_item) {
      $purchased_entity = $order_item->getPurchasedEntity();
      if (!$purchased_entity) {
        continue;
      }
      if ($purchased_entity instanceof ProductVariationInterface) {
        $purchased_entity = $purchased_entity->getProduct();
      }
      foreach ($related_field_names as $related_field_name => $field_settings) {
        if ($purchased_entity->hasField($related_field_name)) {
          $related_products = $purchased_entity->get($related_field_name)->getValue();
          $related_products_reference_type = $purchased_entity->get($related_field_name)->getFieldDefinition()->getTargetEntityTypeId();
        }
        if (!empty($related_products)) {
          foreach ($related_products as $related_product) {
            // If the reference is of type product, then load all variations for each product and suggest them.
            if ($related_products_reference_type == 'commerce_product') {
              /** @var \Drupal\commerce_product\Entity\Product $related_product */
              $related_product = Product::load($related_product['target_id']);
              foreach ($related_product->getVariations() as $related_variation) {
                $suggested_products[$related_variation->id()] = $related_variation;
              }
            }
            // If the reference is of type variation, just suggest all of them.
            else {
              /** @var \Drupal\commerce_product\Entity\ProductVariation $related_variation */
              $related_variation = ProductVariation::load($related_product['target_id']);
              $suggested_products[$related_variation->id()] = $related_variation;
            }
          }
        }
      }
    }

    $additional_data = [];
    $additional_data['related_products'] = array_values($suggested_products);
    $event->addAdditionalData($additional_data);
  }

}
