<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class IqCommerceFeeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'iq_commerce.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'iq_commerce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('iq_commerce.settings');

    $form['minimum-treshold'] = array(
      '#type' => 'number',
      '#title' => $this->t('Minimum amount for additional costs'),
      '#default_value' => $config->get('minimum-treshold') ?? 0,
      '#description' => $this->t('If the cart value is below this value additional costs apply.'),
      '#required' => TRUE,
    );

    $form['additional-cost-amount'] = array(
      '#type' => 'number',
      '#title' => $this->t('Additional cost amount'),
      '#default_value' => $config->get('additional-cost-amount') ?? 0,
      '#description' => $this->t('When the cart is below minimum amount above this amount will be added to total costs'),
      '#required' => TRUE,
    );

    return $form;
  }

  /**

   * {@inheritdoc}

   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('iq_commerce.settings');

    $config->set('threshold', $form_state->getValue('threshold'));
    $config->set('minimum-treshold', $form_state->getValue('minimum-treshold'));
    $config->set('additional-cost-amount', $form_state->getValue('additional-cost-amount'));


    $config->save();

    return parent::submitForm($form, $form_state);

  }

}
