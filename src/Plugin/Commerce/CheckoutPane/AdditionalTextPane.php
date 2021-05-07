<?php

namespace Drupal\iq_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a checkout message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "iq_commerce_pane_additional_text",
 *   label = @Translation("Additional Text"),
 * )
 */
class AdditionalTextPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    $additional_pane_text = "";
    if ($this->configuration['header_text']) {
      $header_size = !empty($this->configuration['header_size']) ? $this->configuration['header_size'] : 1;
      $additional_pane_text .= "<h" . $header_size . ">" . $this->t($this->configuration['header_text']) . "</h" . $header_size . ">";
    }
    if ($this->configuration['additional_text']) {
      if (!empty($additional_pane_text)) {
        $additional_pane_text .= "<br/>";
      }
      $additional_pane_text .= $this->t($this->configuration['additional_text']);
    }
    if (!empty($additional_pane_text)) {
      return $additional_pane_text;
    }
    else {
      return $this->t('No added title or text.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['header_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('The title will be displayed in a header.'),
      '#default_value' => !empty($this->configuration['header_text']) ? $this->configuration['header_text'] : "",
    ];
    $form['header_size'] = [
      '#type' => 'radios',
      '#title' => t('Header Size'),
      '#default_value' => !empty($this->configuration['header_size']) ? $this->configuration['header_size'] : 1,
      '#options' => [
        1 => t('Header 1'),
        2 => t('Header 2'),
        3 => t('Header 3'),
        4 => t('Header 4'),
        5 => t('Header 5'),
        6 => t('Header 6')
      ],
    ];

    $form['additional_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Additional text'),
      '#description' => $this->t('The additional text will be displayed below the header.'),
      '#default_value' => !empty($this->configuration['additional_text']) ? $this->configuration['additional_text'] : "",
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
      $this->configuration['header_text'] = NULL;
      $this->configuration['header_size'] = NULL;
      $this->configuration['additional_text'] = NULL;

      if ($values['header_text']) {
        $this->configuration['header_text'] = $values['header_text'];
      }
      else {
        $this->configuration['header_text'] = "";
      }
      if ($values['header_size']) {
        $this->configuration['header_size'] = $values['header_size'];
      }
      if ($values['additional_text']) {
        $this->configuration['additional_text'] = $values['additional_text'];
      }
      else {
        $this->configuration['additional_text'] = "";
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $header_size = $this->configuration['header_size'];
    $header_text = $this->configuration['header_text'];
    $additional_text = $this->configuration['additional_text'];
    $message = "";
    if (!empty($header_text)) {
      $message = "<h" . $header_size .">" . $this->t($header_text) . "</h" . $header_size . "><br/>" ;
    }
    if (!empty($additional_text)) {
      $message .= "<p>" . $this->t($additional_text) . "</p><br/>";
    }

    $pane_form['message'] = [
      '#markup' => $this->t($message)
    ];
    return $pane_form;
  }


}
