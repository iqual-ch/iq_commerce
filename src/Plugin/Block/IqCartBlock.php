<?php

namespace Drupal\iq_commerce\Plugin\Block;

use Drupal\commerce_cart\Plugin\Block\CartBlock;
use Drupal\Core\Form\FormStateInterface;

/**
 * Extendeds CartBlock with a configurable title.
 *
 * @Block(
 *   id = "iq_commerce_cart",
 *   admin_label = @Translation("Dropdown Cart"),
 *   category = @Translation("Commerce")
 * )
 */
class IqCartBlock extends CartBlock {

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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $render = parent::build();
    // $render['#count_text'] = $this->t($this->configuration['cart_title'], [
    //   '%count' => $render['#count'],
    // ]);
    // return $render;

    $render['#theme'] = 'dropdown_cart_block';
    $render['#cache'] = [
      'max-age' => 0,
    ];
    $render['#count_text'] = $this->t($this->configuration['cart_title'], [
      '%count' => $render['#count'],
    ]);

    $render['#attached'] = [
      'library' => [
        'iq_commerce/dropdown_cart_block',
      ]
    ];

    return $render;

  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['cart_title'] = $form_state->getValue('cart_title');
  }

}
