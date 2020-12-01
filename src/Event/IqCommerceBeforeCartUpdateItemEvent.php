<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the before cart update item event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceBeforeCartUpdateItemEvent extends Event {

  /**
   * The order that is being edited.
   */
  protected $commerceOrder;

  /**
   * The ordered item that is being updated from the cart.
   */
  protected $commerceOrderItem;

  /**
   * @var    $unserialized
   *   The unserialized data from the request body.
   */
  protected $unserialized;

  /**
   * Constructs a new BeforeCartUpdateItemEvent.
   *
   * @param $commerce_order
   *   The order that is being edited.
   * @param $commerce_order_item
   *   The order item that is being updated.
   */
  public function __construct($commerce_order, $commerce_order_item, $unserialized) {
    $this->commerceOrder = $commerce_order;
    $this->commerceOrderItem = $commerce_order_item;
    $this->unserialized = $unserialized;
    \Drupal::logger('iq_commerce')->notice('before cart updated created event');

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
    return $this->commerceOrderItem;
  }

  /**
   * Gets the unseralized data.
   */
  public function getUnserialized() {
    return $this->unserialized;
  }
}
