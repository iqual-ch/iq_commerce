<?php

namespace Drupal\iq_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * IQ Commerce User account setting controller.
 */
class UserController extends ControllerBase {

  /**
   *  Redirects user based on content access for /user/orders page.
   */
  public function userOrdersPage() {
    $user = \Drupal::currentUser();
    if ($user->isAnonymous()) {
      $response = new RedirectResponse(Url::fromUserInput('/user/login')->toString(), 302);
      return $response;
    }
    $response = new RedirectResponse(Url::fromUserInput('/user/' . $user->id() . '/orders')->toString(), 302);
    return $response;
  }
}
