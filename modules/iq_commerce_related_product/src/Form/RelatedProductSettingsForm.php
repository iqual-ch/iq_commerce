<?php

namespace Drupal\iq_commerce_related_product\Form;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Serialization\Yaml as YamlSerializer;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class RelatedProductSettingsForm.
 *
 * @package Drupal\iq_commerce\Form
 */
class RelatedProductSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iq_commerce_related_product_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('iq_commerce_related_product.settings');
    $form['related_product_fields'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Related products settings (list of fields)'),
      '#description' => $this->t('This text field should be written in yml syntax (main key is "related").'),
      '#default_value' => Yaml::decode($config->get('related_product_fields')),
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
      Yaml::encode($form_state->getValue('related_product_fields'));
    }
    catch (InvalidDataTypeException $e) {
      $form_state->setErrorByName(
        'related_product_fields',
        $this->t('The provided configuration is not a valid yaml text.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('iq_commerce_related_product.settings')
      ->set('related_product_fields', Yaml::encode($form_state->getValue('related_product_fields')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get Editable config names.
   *
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return ['iq_commerce_related_product.settings'];
  }

  /**
   * Helper function to return the yml product settings parsed in array.
   */
  public static function getSettings() {
    $config = \Drupal::config('iq_commerce_related_product.settings');
    return YamlParser::parse(YamlSerializer::decode($config->get('related_product_fields')));
  }

}
