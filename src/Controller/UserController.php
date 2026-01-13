<?php

namespace Drupal\iq_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * IQ Commerce User controller.
 *
 * Redirect anonymous users to login page
 * before accessing user-specific pages.
 */
class UserController extends ControllerBase {

  /**
   * Redirect to current user's edit page.
   */
  public function userEditRedirect() {
    // If user is not logged in, redirect to login page.
    if ($this->currentUser()->isAnonymous()) {
      $login_url = Url::fromRoute('user.login', [], [
        'query' => [
          'destination' => '/user/edit',
        ],
      ]);
      $response = new RedirectResponse($login_url->toString(), 302);
      return $response;
    }

    // Redirect to the user's edit page.
    $user_id = $this->currentUser()->id();
    $response = new RedirectResponse(Url::fromRoute('entity.user.edit_form', ['user' => $user_id])->toString(), 302);
    return $response;
  }

  /**
   * Redirect to current user's orders page.
   */
  public function userOrdersRedirect() {

    // If user is not logged in, redirect to login page.
    if ($this->currentUser()->isAnonymous()) {
      $login_url = Url::fromRoute('user.login', [], [
        'query' => [
          'destination' => '/user/orders',
        ],
      ]);
      $response = new RedirectResponse($login_url->toString(), 302);
      return $response;
    }

    // Redirect to the user's orders page.
    $user_id = $this->currentUser()->id();
    $response = new RedirectResponse(Url::fromRoute('view.commerce_user_orders.order_page', ['user' => $user_id])->toString(), 302);
    
    return $response;
  }

}
