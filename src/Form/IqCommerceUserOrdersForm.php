<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class IqCommerceUserOrdersForm extends FormBase {

  public function getFormId()
  {
    // TODO: Implement getFormId() method.
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state)
  {
    /** @var User $user */
    $user = User::load(\Drupal::currentUser()->id());
    $user_id = $user->id();
    if ($user->isAuthenticated()) {
      $resetURL = 'https://' . IqCommerceUserOrdersForm::getDomain() . '/user/' . $user_id .'/orders';
      // @todo if there is a destination, attach it to the url
      $response = new RedirectResponse($resetURL, 302);
      $response->send();
    }
    else {
      $resetURL = 'https://' . IqCommerceUserOrdersForm::getDomain() . '/user/login';
      $response = new RedirectResponse($resetURL, 302);
      $response->send();
    }
    return;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }
  /**
   * Helper function to get domain of the server.
   */
  public static function getDomain() {
    if (!empty($_SERVER["HTTP_HOST"]) || getenv("VIRTUAL_HOSTS")) {
      $virtual_host = "";
      if (getenv("VIRTUAL_HOSTS")) {
        $virtual_hosts = explode(",", getenv("VIRTUAL_HOSTS"));

        if (count($virtual_hosts) > 1) {
          $virtual_host = $virtual_hosts[1];
        } else {
          $virtual_host = $virtual_hosts[0];
        }
      }
      $domain = empty($virtual_host) ? $_SERVER["HTTP_HOST"] : $virtual_host;
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $domain .= '/' . $language;
    }
    return $domain;
  }
}
