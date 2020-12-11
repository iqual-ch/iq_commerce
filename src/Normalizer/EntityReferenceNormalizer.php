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
    parent::__construct($entity_repository, $route_match, $commerce_cart_api);
  }
}