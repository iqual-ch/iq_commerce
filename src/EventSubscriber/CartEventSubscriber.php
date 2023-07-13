<?php

namespace Drupal\iq_commerce\EventSubscriber;

use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\commerce_cart\Event\CartOrderItemRemoveEvent;
use Drupal\commerce_cart\Event\CartOrderItemUpdateEvent;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_price\Price;
use Drupal\commerce_repeat_order\Event\OrderCloneEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Cart Event Subscriber.
 */
class CartEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The tempstore service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempStore;

  /**
   * Constructs a new CartEventSubscriber.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore service.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStore = $temp_store_factory->get('top_events_commerce');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      CartEvents::CART_ENTITY_ADD => ['onCartEntityAdd', -50],
      CartEvents::CART_ORDER_ITEM_REMOVE => ['onCartOrderItemRemove', -50],
      CartEvents::CART_ORDER_ITEM_UPDATE => ['onCartItemUpdate', -50],
      OrderCloneEvent::ORDER_CLONED => ['onCartCloned', -50],
    ];
    return $events;
  }

  /**
   * On Cart Entity Add.
   *
   * Resets the checkout flow status when an item is added to the cart and adds
   * the cleaning price as an adjustment.
   *
   * @param \Drupal\commerce_cart\Event\CartEntityAddEvent $event
   *   The cart event.
   */
  public function onCartEntityAdd(CartEntityAddEvent $event) {
    $cart = $event->getCart();
    $this->checkForMinimalAmountFee($cart);

    if ($cart->hasField('checkout_flow')) {
      $cart->set('checkout_flow', NULL);
    }
  }

  /**
   * On Cart Order Item Remove.
   *
   * @param \Drupal\commerce_cart\Event\CartOrderItemRemoveEvent $event
   *   The event parameter.
   */
  public function onCartOrderItemRemove(CartOrderItemRemoveEvent $event) {
    $cart = $event->getCart();

    if ($cart->hasField('checkout_flow')) {
      $cart->set('checkout_flow', NULL);
    }

    $this->checkForMinimalAmountFee($cart);
  }

  /**
   * On Cart Order Item Update Event.
   *
   * @param \Drupal\commerce_cart\Event\CartOrderItemUpdateEvent $event
   *   The event parameter.
   */
  public function onCartItemUpdate(CartOrderItemUpdateEvent $event) {
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
        'label' => $this->t('Min. fee'),
        'amount' => new Price(\Drupal::config('iq_commerce.settings')->get('additional-cost-amount'), 'CHF'),
        'included' => FALSE,
        'locked' => TRUE,
      ]));
    }
  }

  /**
   * Subscriber for repeat order cart clone. Writes message to tempstorage.
   *
   * @param \Drupal\commerce_repeat_order\Event\OrderCloneEvent $event
   *   Event object parameter.
   */
  public function onCartCloned(OrderCloneEvent $event) {
    $originalOrderItems = $event->getOriginal()->getItems();
    $message = '';
    foreach ($originalOrderItems as $order_item) {
      // Creating new duplicate order item for adding in cart.
      /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $variation */
      $variation = $order_item->getPurchasedEntity();
      $product = $variation ? $variation->getProduct() : NULL;
      if ($product == NULL || !$product->isPublished()) {
        $message = $this->t("Some products weren't copied to the cart as they aren't currently available.");
        $this->tempStore->set('unavailable_products', $message);
      }
    }
  }

}
