<?php

namespace Drupal\iq_commerce_related_product\EventSubscriber;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iq_commerce\Event\IqCommerceCartEvents;
use Drupal\iq_commerce\Form\IqCommerceProductSettingsForm;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\iq_commerce\Event\IqCommerceAfterCartAddEvent;

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
   *
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager,EntityRepositoryInterface $entity_repository) {
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
    $iqCommerceProductSettingsConfig = IqCommerceProductSettingsForm::getIqCommerceProductSettings();
    $related_field_names = $iqCommerceProductSettingsConfig['related'];

    /** @var \Drupal\commerce_order\Entity\OrderItem $order_item */
    foreach ($order_items as $order_item) {
      /** @var \Drupal\commerce_product\Entity\ProductVariation $purchased_entity */
      $purchased_entity = $order_item->getPurchasedEntity();
      foreach ($related_field_names as $related_field_name => $field_settings) {
        if ($purchased_entity->hasField($related_field_name)) {
          $related_products = $purchased_entity->get($related_field_name)->getValue();
          foreach ($related_products as $related_product) {
            /** @var ProductVariation $related_product */
            $related_product = ProductVariation::load($related_product['target_id']);
            $suggested_products[] = $related_product;
          }
        }
      }
    }

    $additional_data = [];
    $additional_data['related_products'] = array_values($suggested_products);
    $event->addAdditionalData($additional_data);
  }
}
