<?php

namespace Drupal\iq_commerce_order_additional_fields\Plugin\Commerce\CheckoutPane;

use Drupal\commerce\AjaxFormTrait;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;

/**
 * Allows customers to add comments to the order.
 *
 * @CommerceCheckoutPane(
 *   id = "collection_date_pane",
 *   label = @Translation("Collection Date"),
 *   default_step = "order_information",
 *   wrapper_element = "fieldset",
 * )
 */
class CollectionDatePane extends CheckoutPaneBase implements CheckoutPaneInterface {

  use AjaxFormTrait;

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form = [];
    if ($this->order->hasField('field_iq_commerce_collect_date')) {
      $pane_form['collection_date'] = [
        '#type' => 'single_date_time',
        '#title' => $this->t('Collection date'),
        '#default_value' => $this->order->field_iq_commerce_collect_date->value,
        '#required' => FALSE,
        '#max_date' => \Drupal::service('date.formatter')->format(strtotime('+90 days'), 'custom', 'd.m.Y'),
        '#min_date' => \Drupal::service('date.formatter')->format(\Drupal::time()->getRequestTime(), 'custom', 'd.m.Y'),
        '#date_timezone' => date_default_timezone_get(),
        '#date_type' => 'date',
        '#time' => FALSE,
        '#hour_format' => 24,
        '#allow_times' => 60,
        '#inline' => '0',
        '#mask' => FALSE,
        '#datetimepicker_theme' => 'default',
        '#exclude_date' => '',
        '#year_start' => '1970',
        '#year_end' => intval(date('Y') + 1),
        '#date_date_element' => 'date',
        '#date_date_callbacks' => [],
        '#date_time_element' => NULL,
        '#first_day' => \Drupal::config('system.date')->get('first_day'),
        '#allowed_hours' => Json::encode(range(0, 23)),
        '#disable_days' => [],
        '#start_date' => \Drupal::service('date.formatter')->format(time(), 'custom', 'd.m.Y'),
      ];
    }
    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $value = $form_state->getValue($pane_form['#parents']);
    if (!empty($value['collection_date'])) {
      $this->order->set('field_iq_commerce_collect_date', $value['collection_date']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    // @todo Only show if the pick up from store shipping method is selected.
    // @todo This can be done either through ajax or somehow through form alter.
    return TRUE;
  }

}
