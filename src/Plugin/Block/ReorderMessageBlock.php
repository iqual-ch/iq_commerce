<?php

namespace Drupal\iq_commerce\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReorderMessageBlock' Block.
 *
 * @Block(
 *   id = "reorder_message_block",
 *   admin_label = @Translation("Reorder message block"),
 * )
 */
class ReorderMessageBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $tempstore = \Drupal::service('tempstore.private');
    $store = $tempstore->get('top_events_commerce');
    if ($store->get('unavailable_products')) {
      $value = $store->get('unavailable_products');
      $store->delete('unavailable_products');
    }
    else {
      return;
    }
    $message = [
      '#markup' => $value,
    ];

    return $message;
  }

  /**
   * Get Cache Max Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
