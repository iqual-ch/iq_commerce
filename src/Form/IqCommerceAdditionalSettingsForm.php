<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class IqCommerceAdditionalSettingsForm.
 *
 * @package Drupal\iq_commerce\Form
 */
class IqCommerceAdditionalSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iq_commerce_additional_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $iqCommerceSettings = static::getIqCommerceAdditionalSettings();
    $form['cart_header'] = [
      '#type'       => 'text_format',
      '#format' => 'pagedesigner',
      '#title' => $this->t('Cart Header'),
      '#description' => $this->t('Add a header for the cart view, leave empty so it will NOT be shown.'),
      '#default_value' => !empty($iqCommerceSettings['cart_header']) ? $iqCommerceSettings['cart_header'] : "",
      '#token_types' => ['commerce_order', 'commerce_payment'],
    ];
    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['commerce_order', 'commerce_payment'],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('iq_commerce.settings')
      ->set('cart_header', $form_state->getValue('cart_header')['value'])
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get Editable config names.
   *
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return ['iq_commerce.settings'];
  }

  /**
   * Helper function to get the iq_commerce settings.
   */
  public static function getIqCommerceAdditionalSettings() {
    $iqCommerceSettingsConfig = \Drupal::config('iq_commerce.settings');
    return [
      'cart_header' => $iqCommerceSettingsConfig->get('cart_header') != NULL ? $iqCommerceSettingsConfig->get('cart_header') : "",
    ];
  }

}
