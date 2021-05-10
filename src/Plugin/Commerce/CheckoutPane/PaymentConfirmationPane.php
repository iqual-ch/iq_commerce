<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a checkout message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "iq_commerce_pane_payment_confirmation",
 *   label = @Translation("Payment Confirmation"),
 * )
 */
class PaymentConfirmationPane extends CheckoutPaneBase {


  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $checkout_flow,
      $container->get('entity_type.manager')
    );
    $instance->setToken($container->get('token'));
    return $instance;
  }

  /**
   * Sets the token service.
   *
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   */
  public function setToken(Token $token) {
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $message = !empty($this->configuration['header']['value']) ? $this->configuration['header']['value'] : "";
    $message = $this->token->replace($this->configuration['header']['value'], [
      'commerce_order' => $this->order,
    ]);
    $pane_form['message'] = [
      '#markup' => $this->t($message)
    ];
    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    $additional_pane_text = "";
    if (!empty($this->configuration['header']['value'])) {
      $additional_pane_text .= htmlspecialchars(substr($this->configuration['header']['value'],0,120)) . "...";
      return $additional_pane_text;
    }
    else {
      return $this->t('No added message.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $default_message = $this->t("<h1>Thank you for your order.</h1>The order number is : [commerce_order:order_number]<br/>");
    if (\Drupal::currentUser()->isAuthenticated()) {
      $default_message .= $this->t("You can view your orders <a href='/user/orders'>here</a><br/>");
    }
    else {
      $default_message .= $this->t("You can see your order when you are logged in.<br/>Continue to <a href='/user/login'>login</a><br/>");
    }
    $default_message .= $this->t("Continue to <a href='/de'>homepage</a><br/>If you have any questions, you can reach us at <a href='/contact'>Contact</a>");

    $form['header'] = [
      '#type'       => 'text_format',
      '#format' => 'pagedesigner',
      '#title' => $this->t('Header'),
      '#description' => $this->t('Add a header message for the receipt.'),
      '#default_value' => !empty($this->configuration['header']['value']) ? $this->configuration['header']['value'] : $default_message
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['header'] = NULL;

      if ($values['header']) {
        $this->configuration['header'] = $values['header'];
      }
      else {
        $this->configuration['header'] = "";
      }
    }
  }


}
