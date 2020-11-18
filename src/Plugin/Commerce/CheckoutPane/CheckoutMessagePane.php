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
    $message = $this->t("<h1>Thank you for your order.</h1>The order number is : @order_number<br/>",['@order_number' => $this->order->getOrderNumber()]);
    if (\Drupal::currentUser()->isAuthenticated()) {
      $message .= $this->t("You can view your orders <a href='/user/orders'>here</a><br/>");
    }
    else {
      $message .= $this->t("You can see your order when you are logged in.<br/>Continue to <a href='/user/login'>login</a><br/>");
    }
    $message .= $this->t("Continue to <a href='/de'>homepage</a><br/>If you have any questions, you can reach us at <a href='/contact'>Contact</a>");

    $pane_form['message'] = [
      '#markup' => $this->t($message)
    ];
    return $pane_form;
  }


}