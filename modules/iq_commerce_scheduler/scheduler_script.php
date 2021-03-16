<?php

die();
$products_to_be_published = \Drupal::entityQuery('commerce_product')->condition('field_iq_commerce_publish_on', NULL, 'IS NOT NULL')
  ->condition('status', 0)->execute();
foreach ($products_to_be_published as $key => $value) {
  $product = Product::load($value);
  $publish_date = $product->field_iq_commerce_publish_on->value;

  if ($publish_date < time()) {
    $product->set('status', 1);
    $product->save();
  }
}
die();
/** @var \Drupal\commerce_product\Entity\Product $product */
$product = \Drupal\commerce_product\Entity\Product::load(852);
$cart_expiration = $product->getThirdPartySetting('iq_commerce_scheduler', 'publish_on');
var_dump($cart_expiration);
$timestamp = time();
$product->setThirdPartySetting('iq_commerce_scheduler','publish_on', $timestamp);
$product->save();
$cart_expiration = $product->getThirdPartySetting('iq_commerce_scheduler', 'publish_on');
var_dump($cart_expiration);
