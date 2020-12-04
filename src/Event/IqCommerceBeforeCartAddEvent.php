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
   * The additional data that was added (usually required products).
   */
  protected $additionalData;

  /**
   * Constructs a new BeforeCartAddEvent.
   *
   * @param $body
   *   The json data of the request to add order item(s) sent to the cart api.
   */
  public function __construct($body) {
    $this->body = $body;
    $this->additionalData = [];
  }

  /**
   * Sets the body.
   */
  public function setBody($body) {
    return $this->body = $body;
  }

  /**
   * Sets the additional data.
   */
  public function setAdditionalData($data) {
    $this->additionalData = $data;
  }

  /**
   * Gets the additional data.
   */
  public function getAdditionalData() {
    return $this->additionalData;
  }

  /**
   * Gets the body.
   */
  public function getBody() {
    return $this->body;
  }
}
