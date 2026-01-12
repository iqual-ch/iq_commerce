<?php

namespace Drupal\iq_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * IQ Commerce User account setting controller.
 */
class UserController extends ControllerBase {

  /**
   * Redirect to user edit page if IQ Group module is not enabled.
   */
  public function userEditPage() {
    // If the IQ Group is not enabled, handle the redirect.
    if (!$this->moduleHandler->moduleExists('iq_group')) {
      $user_id = $this->currentUser->id();
      $response = new RedirectResponse(Url::fromUserInput('/user/' . $user_id . '/edit')->toString(), 302);
      return $response;
    }
    return [];
  }

}
