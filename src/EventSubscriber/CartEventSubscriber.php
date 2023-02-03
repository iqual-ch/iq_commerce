<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Drupal\commerce_repeat_order\Event\OrderCloneEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber to cart events.
 */
class CartEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      OrderCloneEvent::ORDER_CLONED => ['onCartCloned', -50],
    ];
    return $events;
  }

  /**
   * Subscriber for repeat order cart clone. Writes message to tempstorage.
   *
   * @param \Drupal\commerce_repeat_order\Event\OrderCloneEvent $event
   *   Event object parameter.
   */
  public function onCartCloned(OrderCloneEvent $event) {
    $originalOrderItems = $event->getOriginal()->getItems();
    $message = '';
    foreach ($originalOrderItems as $order_item) {
      // Creating new duplicate order item for adding in cart.
      /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $variation */
      $variation = $order_item->getPurchasedEntity();
      $product = $variation ? $variation->getProduct() : NULL;
      if ($product == NULL || !$product->isPublished()) {
        $message = t("Some products weren't copied to the cart as they aren't currently available.");
        $tempstore = \Drupal::service('tempstore.private');
        $store = $tempstore->get('top_events_commerce');
        $store->set('unavailable_products', $message);
      }
    }
  }

}
