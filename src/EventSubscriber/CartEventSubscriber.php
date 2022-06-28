<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\commerce_cart\Event\CartOrderItemRemoveEvent;
use Drupal\commerce_cart\Event\CartOrderItemUpdateEvent;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_repeat_order\Event\OrderCloneEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_order\Entity\OrderInterface;

class CartEventSubscriber implements EventSubscriberInterface
{

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events = [
      CartEvents::CART_ENTITY_ADD => ['onCartEntityAdd', -50],
      CartEvents::CART_ORDER_ITEM_REMOVE => ['onCartOrderItemRemove', -50],
      CartEvents::CART_ORDER_ITEM_UPDATE => ['onCartItemUpdate', -50],
    ];
    return $events;
  }

  /**
   * Resets the checkout flow status when an item is added to the cart and adds the cleaning price as an adjustment.
   *
   * @param \Drupal\commerce_cart\Event\CartEntityAddEvent $event
   *   The cart event.
   */
  public function onCartEntityAdd(CartEntityAddEvent $event)
  {
    $cart = $event->getCart();
    $added_entity = $event->getEntity();
    $this->checkForMinimalAmountFee($cart);

    if ($cart->hasField('checkout_flow')) {
      $cart->set('checkout_flow', NULL);
    }
  }

  public function onCartOrderItemRemove(CartOrderItemRemoveEvent $event)
  {
    $cart = $event->getCart();

    if ($cart->hasField('checkout_flow')) {
      $cart->set('checkout_flow', NULL);
    }

    $this->checkForMinimalAmountFee($cart);
  }

  public function onCartItemUpdate(CartOrderItemUpdateEvent $event)
  {
    $cart = $event->getCart();
    $this->checkForMinimalAmountFee($cart);

    if ($cart->hasField('checkout_flow')) {
      $cart->set('checkout_flow', NULL);
    }
  }

  /**
   * Adds minimal amout fee to the cart price.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $cart
   *   Current users cart.
   */
  public function checkForMinimalAmountFee(OrderInterface $cart) {
    $adjustments = $cart->getAdjustments();
    foreach ($adjustments as $adjustment) {
      if ($adjustment->getType() === 'min_order_fee') {
        $cart->removeAdjustment($adjustment);
      }
    }

    if (count($cart->getItems()) == 0) {
      return;
    }

    if ($cart->getTotalPrice()->getNumber() < \Drupal::config('iq_commerce.settings')->get('minimum-treshold')) {
      $cart->addAdjustment(new Adjustment([
        'type' => 'min_order_fee',
        'label' => t('Min. fee'),
        'amount' => new Price(\Drupal::config('iq_commerce.settings')->get('additional-cost-amount'), 'CHF'),
        'included' => FALSE,
        'locked' => TRUE,
      ]));
    }
  }

}
