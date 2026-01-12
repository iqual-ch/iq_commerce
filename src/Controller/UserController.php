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
   * Redirect to current user's orders page.
   */
  public function userOrdersRedirect() {
    $user_id = $this->currentUser()->id();

    // If user is not logged in, redirect to login page.
    if ($user_id == 0) {
      $response = new RedirectResponse(Url::fromRoute('user.login')->toString(), 302);
      return $response;
    }

    // Redirect to the user's orders page.
    $response = new RedirectResponse(Url::fromUserInput('/user/' . $user_id . '/orders')->toString(), 302);
    return $response;
  }

}
