<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the cart empty event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceAfterCartAddEvent extends Event {



  /**
   * The response after the product item(s) is added to the cart.
   */
  protected $response;

  /**
   * Constructs a new AfterCartAddEvent.
   *
   * @param $response
   *   The response after the order item(s) is added to the cart.
   */
  public function __construct($response) {
    $this->response = $response;
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
