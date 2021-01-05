<?php

namespace Drupal\iq_commerce_related_product\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_progressive_decoupler\Plugin\Block\DecoupledBlockBase;

/**
 * Related product block.
 *
 * @Block(
 *   id = "iq_commerce_related_product_block",
 *   admin_label = @Translation("Related product Block"),
 * )
 */
class RelatedProductBlock extends DecoupledBlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['oververlay_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => isset($this->configuration['oververlay_title']) ? $this->configuration['oververlay_title'] : 'Additional products',
    ];

    $form['oververlay_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => isset($this->configuration['oververlay_title']) ? $this->configuration['oververlay_title'] : 'Additional products',
    ];

    $form['oververlay_label_close'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => isset($this->configuration['oververlay_label_close']) ? $this->configuration['oververlay_label_close'] : 'Close',
    ];

    $form['oververlay_label_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => isset($this->configuration['oververlay_label_cart']) ? $this->configuration['oververlay_label_cart'] : 'Go to cart',
    ];

    $form['oververlay_link_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Cart'),
      '#default_value' => isset($this->configuration['oververlay_link_cart']) ? $this->configuration['oververlay_link_cart'] : '/cart',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $build['#theme'] = 'iq_commerce_related_product_block';
    $build['#attached']['library'][] = 'iq_commerce_related_product/related-product';
    $build['#oververlay_title'] = $this->configuration['oververlay_title'];
    $build['#oververlay_label_close'] = $this->configuration['oververlay_label_close'];
    $build['#oververlay_label_cart'] = $this->configuration['oververlay_label_cart'];
    $build['#oververlay_link_cart'] = $this->configuration['oververlay_link_cart'];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['oververlay_title'] = $form_state->getValue('oververlay_title');
    $this->configuration['oververlay_label_close'] = $form_state->getValue('oververlay_label_close');
    $this->configuration['oververlay_label_cart'] = $form_state->getValue('oververlay_label_cart');
    $this->configuration['oververlay_link_cart'] = $form_state->getValue('oververlay_link_cart');
  }

}
