<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Allows customers to add comments to the order.
 *
 * @CommerceCheckoutPane(
 *   id = "customer_comments",
 *   label = @Translation("Additional Information"),
 *   default_step = "order_information",
 *   wrapper_element = "fieldset",
 * )
 */
class OrderComment extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    if ($this->order->hasField('field_iq_commerce_comment ')) {
      $pane_form['customer_comments'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Order notes'),
        '#default_value' => $this->order->get('field_iq_commerce_comment')->getString(),
        '#required' => FALSE,
      ];
    }
    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $value = $form_state->getValue($pane_form['#parents']);
    if (!empty($value) && isset($value['customer_comments'])) {
      $this->order->set('field_iq_commerce_comment', $value['customer_comments']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    if ($this->order->hasField('field_iq_commerce_comment')) {
      return ['#markup' => $this->order->get('field_iq_commerce_comment')->getString()];
    }
    else {
      return ['#markup' => ''];
    }
  }

}
