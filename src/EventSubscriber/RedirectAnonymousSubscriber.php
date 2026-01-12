<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface {

  /**
   * Create a new subscriber.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   */
  public function __construct(
    protected AccountProxyInterface $account,
    protected RouteMatchInterface $routeMatch,
  ) {
  }

  /**
   * Redirect anonymous users trying to access the user orders page to login.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event to process.
   */
  public function checkAuthStatus(RequestEvent $event) {
    if ($this->account->isAnonymous() && $this->routeMatch->getRouteName() == 'iq_commerce.user_orders') {
      $response = new RedirectResponse('/user/login', 301);
      $response->send();
      $event->setResponse($response);
      $event->stopPropagation();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = ['checkAuthStatus'];
    return $events;
  }

}
