<?php

namespace Drupal\iq_commerce_ajax_cart\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_progressive_decoupler\Plugin\Block\DecoupledBlockBase;

/**
 * AJAX Cart Block.
 *
 * @Block(
 *   id = "iq_commerce_ajax_cart_block",
 *   admin_label = @Translation("AJAX Cart Block"),
 * )
 */
class AjaxCartBlock extends DecoupledBlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['cart_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => isset($this->configuration['cart_title']) ? $this->configuration['cart_title'] : 'Cart (%count)',
    ];

    $form['link_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Title'),
      '#default_value' => isset($this->configuration['link_title']) ? $this->configuration['link_title'] : 'Cart',
    ];

    $form['button_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Title'),
      '#default_value' => isset($this->configuration['button_title']) ? $this->configuration['button_title'] : 'Go to cart',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $build['#theme'] = 'iq_commerce_ajax_cart_block';
    $build['#attached']['library'][] = 'iq_commerce_ajax_cart/ajax-cart';

    $build['#cache'] = [
      'max-age' => 0,
    ];

    $build['#count_text'] = $this->t($this->configuration['cart_title'], [
      '%count' => 5,
    ]);

    $build['#label'] = $this->label();
    $build['#link_title'] = $this->configuration['link_title'];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['cart_title'] = $form_state->getValue('cart_title');
    $this->configuration['link_title'] = $form_state->getValue('link_title');
    $this->configuration['button_title'] = $form_state->getValue('button_title');
  }

}
