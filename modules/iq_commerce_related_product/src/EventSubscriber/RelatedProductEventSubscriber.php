<?php

namespace Drupal\iq_commerce_related_product\EventSubscriber;

use Drupal\iq_commerce\Event\IqCommerceCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\iq_commerce\Event\IqCommerceAfterCartAddEvent;

class RelatedProductEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      IqCommerceCartEvents::AFTER_CART_ENTITY_ADD => 'suggestRelatedProducts'
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

    \Drupal::logger('iq_commerce')->notice('Products related after');
    // add to the payload -> related and required that were added

  }


}
