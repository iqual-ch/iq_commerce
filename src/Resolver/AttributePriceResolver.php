<?php

namespace Drupal\iq_commerce\Resolver;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce\Context;
use Drupal\commerce_price\Price;
use Drupal\commerce_price\Resolver\PriceResolverInterface;

/**
 * Class Resolver.
 */
class AttributePriceResolver implements PriceResolverInterface {

  /**
   * Constructs a new Resolver object.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity, Context $context) {
    \Drupal::logger('iq_commerce')->notice('logger callled');

    // This is code from the default price resolver.
    $default_price = "0.0";
    $field_name = $context->getData('field_name', 'price');

    if ($field_name == 'price') {
      // Use the price getter to allow custom purchasable entity types to have
      // computed prices that are not backed by a field called "price".
      $default_price = $entity->getPrice();
    }
    elseif ($entity->hasField($field_name) && !$entity->get($field_name)->isEmpty()) {
      $default_price = $entity->get($field_name)->first()->toPrice();
    }

    $totalPrice = $default_price->getNumber();
    foreach ($entity->getAttributeValues() as $attrib) {
      if (!$attrib->hasField('field_basisprijs')) {
        // Kunnen we niets mee.
        continue;
      }
      $fieldBasisPrijs = $attrib->get('field_basisprijs');
      // $fieldBasisPrijs->applyDefaultValue();
      $my_price_value = $fieldBasisPrijs->getValue();
      foreach ($my_price_value as $prval) {
        if (is_array($prval) && array_key_exists('number', $prval)) {
          $totalPrice += $prval['number'];
        }
      }
    }

    if (!$entity->isNew()) {
      $result = new Price("" . $totalPrice, $default_price->getCurrencyCode());
      $entity->setPrice($result);
      $entity->enforceIsNew(TRUE);
    }
    else {
      $result = $default_price;
    }

    return $result;
  }

}
