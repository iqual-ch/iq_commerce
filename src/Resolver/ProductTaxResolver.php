<?php

namespace Drupal\iq_commerce\Resolver;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_tax\Resolver\TaxRateResolverInterface;
use Drupal\commerce_tax\TaxZone;
use Drupal\profile\Entity\ProfileInterface;

/**
 * Returns the tax zone's default tax rate.
 */
class ProductTaxResolver implements TaxRateResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(TaxZone $zone, OrderItemInterface $order_item, ProfileInterface $customer_profile) {
    $rates = $zone->getRates();

    // Get the purchased entity.
    $item = $order_item->getPurchasedEntity();

    // Get the corresponding product.
    /** @var \Drupal\commerce_product\Entity\Product $product */
    $product = $item->getProduct();
    $product_type = ProductType::load($product->bundle());
    $rate_id = $product_type->getThirdPartySetting('iq_commerce', 'tax_rate');

    // Check for a tax rate field in the product type.
    foreach ($rates as $rate) {
      if ($rate->getId() == $rate_id) {
        return $rate;
      }
    }
    /*$product_type = $product->bundle();
    $config = \Drupal::config('iq_commerce.settings');
    $savedTaxRates = $config->get('tax_rate_per_product');

    $rate_id = $savedTaxRates[$product_type];
    if (empty($rate_id)) {
      // Take a rate depending on the product type.
      switch ($product_type) {
        case 'wine':
          $rate_id = 'standard';
          break;
        default:
          // The rate for other product type can be resolved using the default tax
          // rate resolver.
          return NULL;
      }
    }*/


    // If no rate has been found, let the other resolvers try to get it.
    return NULL;
  }

}