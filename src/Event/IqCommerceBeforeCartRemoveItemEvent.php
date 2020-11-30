<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the before cart remove item event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceBeforeCartRemoveItemEvent extends Event {

  /**
   * The order that is being edited.
   */
  protected $commerceOrder;

  /**
   * The ordered item that is being removed from the cart.
   */
  protected $commerceOrderItem;

  /**
   * Constructs a new BeforeCartRemoveItemEvent.
   *
   * @param $commerce_order
   *   The order that is being edited.
   * @param $commerce_order_item
   *   The order item that is being removed.
   */
  public function __construct($commerce_order, $commerce_order_item) {
    $this->commerceOrder = $commerce_order;
    $this->commerceOrderItem = $commerce_order_item;
    \Drupal::logger('iq_commerce')->notice('before cart removed created event');

  }


  /**
   * Gets the order.
   */
  public function getOrder() {
    return $this->commerceOrder;
  }

  /**
   * Gets the order item.
   */
  public function getOrderItem() {
    $this->commerceOrderItem;
  }
}
