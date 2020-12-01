<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the before cart add event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceBeforeCartAddEvent extends Event {



  /**
   * The body with json data which is sent to the cart api.
   */
  protected $body;

  /**
   * Constructs a new BeforeCartAddEvent.
   *
   * @param $body
   *   The json data of the request to add order item(s) sent to the cart api.
   */
  public function __construct($body) {
    $this->body = $body;
  }

  /**
   * Sets the body.
   */
  public function setBody($body) {
    return $this->body = $body;
  }

  /**
   * Gets the body.
   */
  public function getBody() {
    return $this->body;
  }
}
