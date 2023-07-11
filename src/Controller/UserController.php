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
   * Redirects user based on content access for /user/orders page.
   */
  public function userOrdersPage() {
    return new RedirectResponse(Url::fromUserInput('/user/' . \Drupal::currentUser()->id() . '/orders')->toString(), 302);
  }

  /**
   * Redirects user to /user/address-book page if its logged in.
   */
  public function userAddressBookPage() {
    return new RedirectResponse(Url::fromUserInput('/user/' . \Drupal::currentUser()->id() . '/address-book')->toString(), 302);
  }

}
