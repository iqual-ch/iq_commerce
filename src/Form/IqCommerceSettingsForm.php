<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class IqCommerceSettingsForm.
 *
 * @package Drupal\iq_commerce\Form
 */
class IqCommerceSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iq_commerce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $iqCommerceSettings = static::getIqCommerceSettings();
    $form['header'] = [
      '#type'       => 'text_format',
      '#format' => $iqCommerceSettings['header']['format'],
      '#title' => $this->t('Header'),
      '#description' => $this->t('Add a header message for the receipt.'),
      '#default_value' => $iqCommerceSettings['header']['value'],
      '#token_types' => ['commerce_order', 'commerce_payment'],
    ];

    $form['footer'] = [
      '#type'       => 'text_format',
      '#format' => $iqCommerceSettings['footer']['format'],
      '#title' => $this->t('Footer'),
      '#description' => $this->t('Add a footer message for the receipt.'),
      '#default_value' => $iqCommerceSettings['footer']['value'],
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
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $iqCommerceSettingsConfig = \Drupal::languageManager()->getLanguageConfigOverride($current_language, 'iq_commerce.settings');
    $iqCommerceSettingsConfig
      ->set('header', $form_state->getValue('header'))
      ->set('footer', $form_state->getValue('footer'))
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
  public static function getIqCommerceSettings() {
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $iqCommerceSettingsConfig = \Drupal::languageManager()->getLanguageConfigOverride($current_language, 'iq_commerce.settings');
    return [
      'header' => $iqCommerceSettingsConfig->get('header') != NULL ? $iqCommerceSettingsConfig->get('header') : ['value' => '<b>' . t("Commerce Store.") . '</b>', 'format' => 'pagedesigner'],
      'footer' => $iqCommerceSettingsConfig->get('footer') != NULL ? $iqCommerceSettingsConfig->get('footer') : ['value' => t("Thank you for your order. You will receive the item(s) in 3 - 7 days."), 'format' => 'pagedesigner'],
    ];
  }

}
