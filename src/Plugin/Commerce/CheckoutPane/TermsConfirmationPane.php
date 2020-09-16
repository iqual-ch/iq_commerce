<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a checkout message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "iq_commerce_pane_terms_confirmation",
 *   label = @Translation("Terms confirmation"),
 * )
 */
class TermsConfirmationPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['term_confirmation'] = [
      '#type' => 'checkbox',
      '#required' => TRUE,
      '#title' => $this->t('I accept the <a href="node/95">terms and condiditons</a>.'),
    ];
    return $pane_form;
  }

}
