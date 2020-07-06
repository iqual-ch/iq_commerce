<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a checkout message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "iq_commerce_pane_checkout_message",
 *   label = @Translation("Checkout message"),
 * )
 */
class CheckoutMessagePane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['message'] = [
      '#markup' => $this->t("
      <H1>Thank you for your order.</H1>

The order number is : @order_number<br/> 

You can see your order when you are logged in.<br/>

Continue to <a href='/user/login'>login</a><br/>

Continue to <a href='/de'>homepage</a><br/>

If you have any questions, you can reach us at <a href='/contact'>Contact</a>", ['@order_number' => $this->order->getOrderNumber()]),
    ];
    return $pane_form;
  }


}