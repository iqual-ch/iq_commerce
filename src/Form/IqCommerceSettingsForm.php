<?php

namespace Drupal\iq_commerce\Form;

use Drupal\commerce_product\Entity\ProductType;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\RoleInterface;

/**
 * Class IqCommerceSettingsForm.
 *
 * @package Drupal\iq_commerce\Form
 */
class IqCommerceSettingsForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'iq_commerce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('iq_commerce.settings');
    $savedTaxRates = $config->get('tax_rate_per_product');

    $form['tax_rates'] = [
      '#type' => 'markup',
      '#markup' => '<span><b>Options: </b><br/><i>\'standard\'</i> for 7.7%<br/> <i>\'hotel\'</i> for 3.7% <br/><i>\'reduced\'</i> for 2.5%</span>'
    ];
    foreach (ProductType::loadMultiple() as $product_type) {
      $form['product_type'][$product_type->id()] = [
        '#type' => 'textfield',
        '#title' => $product_type->label(),
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => $this->t('Add a tax rate for the product type %s', ['%s' => $product_type->label()]),
        '#default_value' => isset($savedTaxRates[$product_type->id()]) ? $savedTaxRates[$product_type->id()] : 'standard',
      ];
    }
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
    $taxRates = [];
    foreach (ProductType::loadMultiple() as $product_type) {
      $taxRates[$product_type->id()] = $form_state->getValue($product_type->id());
    }
    $this->config('iq_commerce.settings')
      ->set('tax_rate_per_product', $taxRates)
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

}
