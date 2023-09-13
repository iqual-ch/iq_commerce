<?php

namespace Drupal\iq_commerce\Event;

use Drupal\rest\ResourceResponseInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Defines the after cart remove item event.
 *
 * @see \Drupal\iq_commerce\Event\CartEvents
 */
class IqCommerceAfterCartRemoveItemEvent extends Event {

  /**
   * The response after the product item is removed from the cart.
   *
   * @var \Drupal\rest\ResourceResponseInterface
   */
  protected $response;

  /**
   * Constructs a new AfterCartRemoveEvent.
   *
   * @param \Drupal\rest\ResourceResponseInterface $response
   *   The response after the order item is removed from the cart.
   */
  public function __construct(ResourceResponseInterface $response) {
    $this->response = $response;
    \Drupal::logger('iq_commerce')->notice('after cart removed created event');
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
