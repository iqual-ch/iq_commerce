<?php

namespace Drupal\iq_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupRole;
use Drupal\iq_group\Event\MauticEvent;
use Drupal\iq_group\Form\RegisterForm;
use Drupal\iq_group\MauticEvents;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * IQ Commerce Order controller.
 */
class OrderController extends ControllerBase {

  function orderPage() {
    $user_id = \Drupal::currentUser()->id();
    $response = new RedirectResponse(Url::fromUserInput('/user/'.$user_id.'/orders')->toString(), 302);
    $response->send();
    return;

  }

}
