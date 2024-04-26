<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a checkout message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "iq_commerce_pane_age_confirmation",
 *   label = @Translation("Age confirmation"),
 * )
 */
class AgeConfirmationPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['age_confirmation'] = [
      '#type' => 'checkbox',
      '#required' => TRUE,
      '#title' => $this->t('I confirm that I am over 18 years old.'),
    ];
    return $pane_form;
  }

}
