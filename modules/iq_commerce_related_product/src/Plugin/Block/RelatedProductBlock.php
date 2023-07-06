<?php

namespace Drupal\iq_commerce_related_product\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_progressive_decoupler\Plugin\Block\DecoupledBlockBase;
use Drupal\Component\Serialization\Yaml;
use Symfony\Component\Yaml\Yaml as YamlParser;
use Drupal\Component\Serialization\Yaml as YamlSerializer;

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
      '#default_value' => $this->configuration['oververlay_title'] ?? 'Additional products',
    ];

    $form['oververlay_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['oververlay_title'] ?? 'Additional products',
    ];

    $form['oververlay_label_close'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['oververlay_label_close'] ?? 'Close',
    ];

    $form['oververlay_label_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['oververlay_label_cart'] ?? 'Go to cart',
    ];

    $form['oververlay_link_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Cart'),
      '#default_value' => $this->configuration['oververlay_link_cart'] ?? '/cart',
    ];

    $form['ui_pattern_purchased_item'] = [
      '#type' => 'select',
      '#empty_value' => '_none',
      '#title' => $this->t('Added product: Pattern'),
      '#options' => $this->patternsManager->getPatternsOptions(),
      '#default_value' => $this->configuration['ui_pattern_purchased_item'] ?? NULL,
      '#required' => TRUE,
    ];

    $form['field_mapping_purchased_item'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Added product: Field mapping'),
      '#default_value' => isset($this->configuration['field_mapping_purchased_item']) ? Yaml::decode($this->configuration['field_mapping_purchased_item']) : NULL,
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

    $pattern = $this->patternsManager->getDefinitions()[$this->configuration['ui_pattern_purchased_item']];
    foreach ($pattern->getLibrariesNames() as $library) {
      $build['#attached']['library'][] = $library;
    }

    $build['#attached']['drupalSettings']['progressive_decoupler'][$this->configuration['block_id']]['template_purchased_item'] = \file_get_contents($pattern['base path'] . '/' . $pattern['template'] . '.html.twig');
    $build['#attached']['drupalSettings']['progressive_decoupler'][$this->configuration['block_id']]['ui_pattern_purchased_item'] = $this->configuration['ui_pattern_purchased_item'];

    if (isset($this->configuration['field_mapping_purchased_item'])) {
      $build['#attached']['drupalSettings']['progressive_decoupler'][$this->configuration['block_id']]['field_mapping_purchased_item'] = YamlParser::parse(YamlSerializer::decode($this->configuration['field_mapping_purchased_item']));
    }

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
    $this->configuration['ui_pattern_purchased_item'] = $form_state->getValue('ui_pattern_purchased_item');
    $this->configuration['field_mapping_purchased_item'] = Yaml::encode($form_state->getValue('field_mapping_purchased_item'));
  }

}
