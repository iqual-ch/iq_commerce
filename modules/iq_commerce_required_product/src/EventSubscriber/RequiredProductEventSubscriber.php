<?php

namespace Drupal\iq_commerce_required_product\EventSubscriber;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iq_commerce\Event\IqCommerceBeforeCartAddEvent;
use Drupal\iq_commerce\Event\IqCommerceCartEvents;
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
      IqCommerceCartEvents::BEFORE_CART_ENTITY_ADD => 'addRequiredProducts',
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
    $added_products = 0;

    foreach ($body as $order_item_data) {
      $storage = $this->entityTypeManager->getStorage($order_item_data['purchased_entity_type']);

      $purchased_entity = $storage->load($order_item_data['purchased_entity_id']);
      if (!$purchased_entity || !$purchased_entity instanceof PurchasableEntityInterface) {
        continue;
      }
      /** @var \Drupal\commerce_product\Entity\Product $purchased_entity */
      $purchased_entity = $this->entityRepository->getTranslationFromContext($purchased_entity);

      // If the required product is not in the cart, add it to the body.
      if ($purchased_entity->hasField('field_iq_commerce_required')) {
        $required_products = $purchased_entity->get('field_iq_commerce_required')->getValue();
        foreach ($required_products as $required_product) {
          if ($required_product['target_id']) {
            $required_product = ProductVariation::load($required_product['target_id']);
            $added_products++;
            $body[] = [
              'purchased_entity_type' => 'commerce_product_variation',
              'purchased_entity_id' => $required_product->id(),
              'quantity' => (!empty($order_item_data['quantity'])) ? $order_item_data['quantity'] : 1,
            ];
          }
        }
      }
    }
    // If there were any required products that were added, add them to the
    // event's body, so they can be sent to the cart API.
    if ($added_products > 0) {
      $event->setBody($body);
    }
  }
}
