<?php

namespace Drupal\iq_commerce_order_additional_fields\Plugin\Commerce\CheckoutPane;

use Drupal\commerce\AjaxFormTrait;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_order\Entity\Order;
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
        '#type' => 'date',
        '#title' => $this->t('Collection date'),
        '#default_value' => $this->order->field_iq_commerce_collect_date->value,
        '#required' => FALSE,
        '#attributes' => [
          'min' =>  \Drupal::service('date.formatter')->format(\Drupal::time()->getRequestTime(), 'custom', 'Y-m-d'),
          'type' => 'date'
        ],
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
    // @TODO Only show if the pick up from store shipping method is selected.
    // @TODO This can be done either through ajax or somehow through form alter.

    return TRUE;
  }

}
