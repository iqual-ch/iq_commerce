<?php

namespace Drupal\iq_commerce\Form;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Serialization\Yaml as YamlSerializer;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class Iq Commerce Product Settings Form.
 *
 * @package Drupal\iq_commerce\Form
 */
class IqCommerceProductSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iq_commerce_product_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $iqCommerceProductSettingsConfig = \Drupal::config('iq_commerce.product.settings');
    $form['general'] = [
      '#type' => 'textarea',
      '#title' => $this->t('IQ Commerce Product settings'),
      '#description' => $this->t('This text field should be written in yml syntax (main keys are "required" and "related" products).'),
      '#default_value' => $iqCommerceProductSettingsConfig->get('general') ? Yaml::decode($iqCommerceProductSettingsConfig->get('general')) : '',
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    try {
      Yaml::encode($form_state->getValue('general'));
    }
    catch (InvalidDataTypeException $e) {
      $form_state->setErrorByName(
        'general',
        $this->t('The provided configuration is not a valid yaml text.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('iq_commerce.product.settings')
      ->set('general', Yaml::encode($form_state->getValue('general')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get Editable config names.
   *
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return ['iq_commerce.product.settings'];
  }

  /**
   * Helper function to return the yml product settings parsed in array.
   */
  public static function getIqCommerceProductSettings() {
    $iqCommerceProductSettingsConfig = \Drupal::config('iq_commerce.product.settings');
    if (!empty($iqCommerceProductSettingsConfig->get('general'))) {
      return YamlParser::parse(YamlSerializer::decode($iqCommerceProductSettingsConfig->get('general')));
    }
  }

}
