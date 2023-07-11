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
   *
   * @var \Drupal\rest\ResourceResponseInterface
   */
  protected $response;

  /**
   * Constructs a new AfterCartUpdateEvent.
   *
   * @param \Drupal\rest\ResourceResponseInterface $response
   *   The response after the order item is updated from the cart.
   */
  public function __construct($response) {
    $this->response = $response;
  }

  /**
   * Gets the response.
   *
   * @return \Drupal\rest\ResourceResponseInterface
   *   The response from the cart api.
   */
  public function getResponse() {
    return $this->response;
  }

}
