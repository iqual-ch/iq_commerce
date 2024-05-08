<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\redirect_after_login\Event\RedirectAfterLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to the RedirectAfterLoginEvent.
 */
class CheckoutLoginSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a CheckoutRegistration object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   */
  public function __construct(
    protected RouteMatchInterface $routeMatch,
  ) {}

  /**
   * Prevent redirect after login in the Commerce checkout.
   */
  public function onRedirectAfterLogin(RedirectAfterLoginEvent $event) {
    $current_route = $this->routeMatch->getRouteName();
    if ($current_route === 'commerce_checkout.form') {
      $event->setRedirectAllowed(FALSE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [RedirectAfterLoginEvent::class => 'onRedirectAfterLogin'];
  }

}
