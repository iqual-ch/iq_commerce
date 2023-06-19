<?php

namespace Drupal\iq_commerce\Event;

<<<<<<< HEAD
use Symfony\Contracts\EventDispatcher\Event;

=======
>>>>>>> 2.x
/**
 * Defines the before cart update item event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceBeforeCartUpdateItemEvent extends IqCommerceBeforeCartRemoveItemEvent {

  /**
<<<<<<< HEAD
   * The ordered item that is being updated from the cart.
   */
  protected $commerceOrderItem;

  /**
   * @var    unserialized
   *   The unserialized data from the request body.
=======
   * The unserialized data from the request body.
   *
   * @var mixed
>>>>>>> 2.x
   */
  protected $unserialized;

  /**
   * Constructs a new BeforeCartUpdateItemEvent.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $commerce_order
   *   The order that is being edited.
   * @param \Drupal\commerce_order\Entity\OrderItemInterface $commerce_order_item
   *   The order item that is being removed.
   * @param mixed $unserialized
   *   The unserialized data from the request body.
   */
  public function __construct($commerce_order, $commerce_order_item, $unserialized) {
    parent::__construct($commerce_order, $commerce_order_item);
    $this->unserialized = $unserialized;
<<<<<<< HEAD
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
=======

>>>>>>> 2.x
  }

  /**
   * Gets the unseralized data.
   *
   * @return mixed
   *   The unserialized data from the request body.
   */
  public function getUnserialized() {
    return $this->unserialized;
  }

}
