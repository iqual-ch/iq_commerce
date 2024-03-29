<?php

namespace Drupal\iq_commerce\EventSubscriber;

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
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account = NULL;

  /**
   * Create a new subscriber.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   */
  public function __construct(AccountProxyInterface $account) {
    $this->account = $account;
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
