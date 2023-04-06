<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface {

  /**
   *
   */
  public function __construct() {
    $this->account = \Drupal::currentUser();
  }

  /**
   *
   */
  public function checkAuthStatus(RequestEvent $event) {
    if ($this->account->isAnonymous() && \Drupal::routeMatch()->getRouteName() == 'iq_commerce.user_orders') {
      $response = new RedirectResponse('/user/login', 301);
      $response->send();
      $event->setResponse($response);
      $event->stopPropagation();
    }
  }

  /**
   *
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = ['checkAuthStatus'];
    return $events;
  }

}
