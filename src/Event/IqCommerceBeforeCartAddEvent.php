<?php

namespace Drupal\iq_commerce\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Defines the before cart add event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceBeforeCartAddEvent extends Event {

  /**
   * The body with json data which is sent to the cart api.
   *
   * @var array
   */
  protected $body;

  /**
   * The additional data that was added (usually required products).
   *
   * @var array
   */
  protected $additionalData;

  /**
   * Constructs a new BeforeCartAddEvent.
   *
   * @param array $body
   *   The json data of the request to add order item(s) sent to the cart api.
   * @param array $additional_data
   *   The additional data to be added.
   */
  public function __construct($body, $additional_data = []) {
    $this->body = $body;
    $this->additionalData = $additional_data;
  }

  /**
   * Sets the body.
   *
   * @param array $body
   *   The json data of the request to add order item(s) sent to the cart api.
   */
  public function setBody($body) {
    return $this->body = $body;
  }

  /**
   * Sets the additional data.
   *
   * @param array $additional_data
   *   The additional data to be added.
   */
  public function setAdditionalData($data) {
    $this->additionalData = $data;
  }

  /**
   * Gets the additional data.
   *
   * @return array
   *   The additional data.
   */
  public function getAdditionalData() {
    return $this->additionalData;
  }

  /**
   * Gets the body.
   *
   * @return array
   *   The body.
   */
  public function getBody() {
    return $this->body;
  }

}
