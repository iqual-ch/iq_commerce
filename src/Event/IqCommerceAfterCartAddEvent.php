<?php

namespace Drupal\iq_commerce\Event;

use Drupal\rest\ModifiedResourceResponse;
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
   * The additional data to the response.
   */
  protected $additionalData;

  /**
   * Constructs a new AfterCartAddEvent.
   *
   * @param $response
   *   The response after the order item(s) is added to the cart.
   */
  public function __construct($response) {
    $this->response = $response;
    $this->additionalData = [];
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

  /**
   * Gets the response.
   *
   * @return array
   *   The response with the additional data.
   */
  public function getResponseWithAdditionalData() {
    $order_items = $this->response->getResponseData();
    \Drupal::logger('iq_commerce')->notice(json_encode($this->additionalData));
    $data = array_merge(['order_items' => array_values($order_items)], $this->additionalData);
    \Drupal::logger('iq_commerce')->notice(json_encode(array_keys($data)));

    $response = new ModifiedResourceResponse($data, 200);
    return $response;
  }

  /**
   * Adds additional data to the response.
   *
   * @param $data
   *   The additional data to be added.
   */
  public function addAdditionalData($data) {
    $this->additionalData = array_merge($this->additionalData, $data);
  }
}
