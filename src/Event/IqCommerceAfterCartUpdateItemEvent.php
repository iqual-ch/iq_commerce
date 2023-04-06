<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Defines the after cart update item event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceAfterCartUpdateItemEvent extends Event {

  /**
   * The response after the product item is updated from the cart.
   */
  protected $response;

  /**
   * Constructs a new AfterCartUpdateEvent.
   *
   * @param $response
   *   The response after the order item is updated from the cart.
   */
  public function __construct($response) {
    $this->response = $response;
    \Drupal::logger('iq_commerce')->notice('after cart updated created event');
  }

  /**
   * Gets the response.
   *
   * @return array
   *   The response from the cart api.
   */
  public function getResponse() {
    return $this->response;
  }

}
