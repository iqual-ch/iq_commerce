<?php

/**
 * @file
 */

use Drupal\commerce_product\Entity\ProductVariation;

$now = date("Y-m-d", time());
$products_to_be_published = ProductVariation::loadMultiple();
foreach ($products_to_be_published as $key => $product) {
  if ($product->hasField('field_iq_commerce_unpublish_on')) {
    $unpublish_date = $product->field_iq_commerce_unpublish_on->value;
  }
  if ($product->hasField('field_iq_commerce_publish_on')) {
    $publish_date = $product->field_iq_commerce_publish_on->value;
  }
  if (!empty($publish_date) && $product->status->value == 0) {
    if ($publish_date <= $now && (empty($unpublish_date) || $unpublish_date > $now)) {
      $product->setPublished();
      $product->save();
    }
  }
  if (!empty($unpublish_date) && $product->status->value == 1) {
    if ($unpublish_date <= $now) {
      $product->setPublished();
      $product->save();
    }
  }
}
