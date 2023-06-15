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
      '#title' => $this->t($this->configuration['terms_label'], [
        '@terms_link' => $this->configuration['terms_link'],
      ]),
    ];
    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'terms_label' => $this->t('I accept the <a href="@terms_link">terms and conditions</a>.'),
      'terms_link' => '#',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    return $this->t('Terms label: @terms_label <br/>Terms link: @terms_link', [
      '@terms_label' => $this->configuration['terms_label'],
      '@terms_link' => $this->configuration['terms_link'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['terms_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Terms label'),
      '#default_value' => $this->configuration['terms_label'],
    ];

    $form['terms_link'] = [
      '#type' => 'linkit',
      '#title' => $this->t('Terms link'),
      '#autocomplete_route_name' => 'linkit.autocomplete',
      '#autocomplete_route_parameters' => [
        'linkit_profile_id' => 'default_linkit',
      ],
      '#default_value' => $this->configuration['terms_link'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['terms_label'] = $values['terms_label'];
      $this->configuration['terms_link'] = $values['terms_link'];
    }
  }

}
