<?php

namespace Drupal\iq_commerce\Event;

/**
 * Defines events for the iq_commerce module.
 */
final class IqCommerceCartEvents {

  /**
   * Name of the event fired before adding the order item(s) to the cart.
   *
   * Fired before the item(s) is added to the cart api.
   *
   * @Event
   */
  const BEFORE_CART_ENTITY_ADD = 'iq_commerce.before_cart_add';

  /**
   * Name of the event fired after adding the order item(s) to the cart.
   *
   * Fired after the item(s) added to the cart api.
   *
   * @Event
   */
  const AFTER_CART_ENTITY_ADD = 'iq_commerce.after_cart_add';

  /**
   * Name of the event fired before removing the order item from the cart.
   *
   * Fired before the item is removed from the cart.
   *
   * @Event
   */
  const BEFORE_CART_ENTITY_REMOVE_ITEM = 'iq_commerce.before_cart_remove_item';

  /**
   * Name of the event fired after removing the item from the cart.
   *
   * Fired after the item has been removed from the cart.
   *
   * @Event
   */
  const AFTER_CART_ENTITY_REMOVE_ITEM = 'iq_commerce.after_cart_remove_item';

}
