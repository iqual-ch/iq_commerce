<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_commerce\Controller\UserController;

/**
 * Class IqCommerceAdditionalSettingsForm.
 *
 * @package Drupal\iq_commerce\Form
 */
class IqCommerceAdditionalSettingsForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'iq_commerce_additional_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $iqCommerceSettings = $this->getIqCommerceAdditionalSettings();
    $form['cart_header'] = [
      '#type'       => 'textfield',
      '#title' => $this->t('Cart Header'),
      '#description' => $this->t('Add a header for the cart view, leave empty so it will NOT be shown.'),
      '#default_value' => !empty($iqCommerceSettings['cart_header']) ? $iqCommerceSettings['cart_header'] : "",
    ];

    $form['cart_header_size'] = [
      '#type' => 'radios',
      '#title' => t('Cart Header Size'),
      '#default_value' => !empty($iqCommerceSettings['cart_header_size']) ? $iqCommerceSettings['cart_header_size'] : 1,
      '#options' => [
        1 => t('Header 1'),
        2 => t('Header 2'),
        3 => t('Header 3'),
        4 => t('Header 4'),
        5 => t('Header 5'),
        6 => t('Header 6')
      ],
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
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->config('iq_commerce.settings')
      ->set('cart_header', $form_state->getValue('cart_header'))
      ->set('cart_header_size', $form_state->getValue('cart_header_size'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get Editable config names.
   *
   * @inheritDoc
   */
  protected function getEditableConfigNames()
  {
    return ['iq_commerce.settings'];
  }

  /**
   * Helper function to get the iq_commerce settings.
   */
  public static function getIqCommerceAdditionalSettings() {
    $iqCommerceSettingsConfig = \Drupal::config('iq_commerce.settings');
    return [
      'cart_header' => $iqCommerceSettingsConfig->get('cart_header') != NULL ? $iqCommerceSettingsConfig->get('cart_header') : "",
      'cart_header_size' => $iqCommerceSettingsConfig->get('cart_header_size') != NULL ? $iqCommerceSettingsConfig->get('cart_header_size') : 1,
    ];
  }


}
