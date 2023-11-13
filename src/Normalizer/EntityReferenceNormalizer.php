<?php

namespace Drupal\iq_commerce\Normalizer;

use Drupal\commerce_cart_api\Normalizer\EntityReferenceNormalizer as EntityReferenceNormalizerBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\iq_commerce\Form\IqCommerceProductSettingsForm;

/**
 * Class EntityReferenceNormalizer.
 *
 * Extends the EntityReferenceNormalizer from the commerce_cart_api module to
 *   attach the products and images as entities to the serialized response.
 *
 * @package Drupal\iq_commerce\Normalizer
 */
class EntityReferenceNormalizer extends EntityReferenceNormalizerBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityRepositoryInterface $entity_repository, RouteMatchInterface $route_match, array $commerce_cart_api) {
    $config = IqCommerceProductSettingsForm::getIqCommerceProductSettings();
    if (!empty($config['normalize_fields'])) {
      foreach ($config['normalize_fields'] as $field) {
        $commerce_cart_api['normalized_entity_references'][] = $field;
      }
    }
    parent::__construct($entity_repository, $route_match, $commerce_cart_api);
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($field_item, $format = NULL, array $context = []): float|array|\ArrayObject|bool|int|string|null {
    $normalized = parent::normalize($field_item, $format, $context);
    if (!empty($normalized['product_id']) && !empty($normalized['product_id']['product_id'])) {
      $normalized['product_entity'] = $normalized['product_id'];
      unset($normalized['product_id']);
    }
    if (!empty($normalized['uri'])) {
      $normalized['url'] = \Drupal::service('file_url_generator')->generateAbsoluteString($normalized['uri']);
    }
    return $normalized;
  }

}
