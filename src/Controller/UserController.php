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
   *
   */
  public function userEditPage() {
    $moduleHandler = \Drupal::service('module_handler');
    // If the IQ Group is not enabled, handle the redirect.
    if (!$moduleHandler->moduleExists('iq_group')) {
      $user_id = \Drupal::currentUser()->id();
      $response = new RedirectResponse(Url::fromUserInput('/user/' . $user_id . '/edit')->toString(), 302);
      return $response;
    }
    return [];
  }

}
