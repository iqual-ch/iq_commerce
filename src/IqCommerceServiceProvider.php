<?php

namespace Drupal\iq_commerce;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Remove the foo event subscriber.
 */
class IqCommerceServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $container->removeDefinition('commerce_cart.cart_subscriber');
  }

}
