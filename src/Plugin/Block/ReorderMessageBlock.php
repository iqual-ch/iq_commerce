<?php

namespace Drupal\iq_commerce\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ReorderMessageBlock' Block.
 *
 * @Block(
 *   id = "reorder_message_block",
 *   admin_label = @Translation("Reorder message block"),
 * )
 */
class ReorderMessageBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The tempstore service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempStore;

  /**
   * Constructs a ReorderMessageBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    PrivateTempStoreFactory $temp_store_factory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tempStore = $temp_store_factory->get('top_events_commerce');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('tempstore.private')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if ($this->tempStore->get('unavailable_products')) {
      $value = $this->tempStore->get('unavailable_products');
      $this->tempStore->delete('unavailable_products');
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
