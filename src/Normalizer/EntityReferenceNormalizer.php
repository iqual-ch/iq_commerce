<?php

namespace Drupal\iq_commerce\Normalizer;

/**
 * Class EntityReferenceNormalizer
 *
 * Extends the EntityReferenceNormalizer from the commerce_cart_api module to
 *   attach the products and images as entities to the serialized response.
 *
 * @package Drupal\iq_commerce\Normalizer
 */
class EntityReferenceNormalizer extends \Drupal\commerce_cart_api\Normalizer\EntityReferenceNormalizer {
  public function __construct(\Drupal\Core\Entity\EntityRepositoryInterface $entity_repository, \Drupal\Core\Routing\RouteMatchInterface $route_match, array $commerce_cart_api) {
    $commerce_cart_api['normalized_entity_references'][] = 'product_id';
    $commerce_cart_api['normalized_entity_references'][] = 'field_iq_commerce_images';
    $commerce_cart_api['normalized_entity_references'][] = 'field_media_image';
    parent::__construct($entity_repository, $route_match, $commerce_cart_api);
  }

  public function normalize($field_item, $format = NULL, array $context = []) {
    $normalized =  parent::normalize($field_item, $format, $context);
    if (!empty($normalized['product_id']) && !empty($normalized['product_id']['product_id']) ) {
      $normalized['product_entity'] = $normalized['product_id'];
      unset($normalized['product_id']);
    }
    if (!empty($normalized['uri'])) {
      $normalized['url'] = file_create_url($normalized['uri']);
    }
    return $normalized;
  }

}
