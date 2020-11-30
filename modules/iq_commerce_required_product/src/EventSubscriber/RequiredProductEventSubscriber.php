<?php

namespace Drupal\iq_commerce_required_product\EventSubscriber;

use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_order\Resolver\ChainOrderTypeResolverInterface;
use Drupal\commerce_price\Resolver\ChainPriceResolverInterface;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\iq_commerce\Event\IqCommerceBeforeCartAddEvent;
use Drupal\iq_commerce\Event\IqCommerceCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequiredProductEventSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The order item store.
   *
   * @var \Drupal\commerce_order\OrderItemStorageInterface
   */
  protected $orderItemStorage;

  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The current store.
   *
   * @var \Drupal\commerce_store\CurrentStoreInterface
   */
  protected $currentStore;

  /**
   * The chain order type resolver.
   *
   * @var \Drupal\commerce_order\Resolver\ChainOrderTypeResolverInterface
   */
  protected $chainOrderTypeResolver;

  /**
   * Constructs a new RequiredProductEventSubscriber object.
   *
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_order\Resolver\ChainOrderTypeResolverInterface $chain_order_type_resolver
   *   The chain order type resolver.
   * @param \Drupal\commerce_store\CurrentStoreInterface $current_store
   *   The current store.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(CartProviderInterface $cart_provider, EntityTypeManagerInterface $entity_type_manager, ChainOrderTypeResolverInterface $chain_order_type_resolver, CurrentStoreInterface $current_store, AccountInterface $current_user, EntityRepositoryInterface $entity_repository) {
    $this->entityTypeManager = $entity_type_manager;
    $this->orderItemStorage = $entity_type_manager->getStorage('commerce_order_item');
    $this->chainOrderTypeResolver = $chain_order_type_resolver;
    $this->currentStore = $current_store;
    $this->currentUser = $current_user;
    $this->entityRepository = $entity_repository;
    $this->cartProvider = $cart_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      IqCommerceCartEvents::BEFORE_CART_ENTITY_ADD => 'addRequiredProducts',
    ];
    return $events;
  }

  /**
   * Adds order items to the cart which are required.
   *
   * @param \Drupal\iq_commerce\Event\IqCommerceBeforeCartAddEvent $event
   *   The before add to cart event.
   */
  public function addRequiredProducts(IqCommerceBeforeCartAddEvent $event) {

    \Drupal::logger('iq_commerce')->notice('Products added in before');
    $body = $event->getBody();
    $added_products = 0;
    \Drupal::logger('iq_commerce')->notice(json_encode($body));

    foreach ($body as $order_item_data) {
      $storage = $this->entityTypeManager->getStorage($order_item_data['purchased_entity_type']);

      $purchased_entity = $storage->load($order_item_data['purchased_entity_id']);
      if (!$purchased_entity || !$purchased_entity instanceof PurchasableEntityInterface) {
        continue;
      }
      \Drupal::logger('iq_commerce')->notice('We got here');
      \Drupal::logger('iq_commerce')->notice($purchased_entity->id());
      /** @var \Drupal\commerce_product\Entity\Product $purchased_entity */
      $purchased_entity = $this->entityRepository->getTranslationFromContext($purchased_entity);
      $store = $this->selectStore($purchased_entity);
      $order_item = $this->orderItemStorage->createFromPurchasableEntity($purchased_entity, [
        'quantity' => (!empty($order_item_data['quantity'])) ? $order_item_data['quantity'] : 1,
      ]);
      $context = new Context($this->currentUser, $store);

      $order_type_id = $this->chainOrderTypeResolver->resolve($order_item);
      $cart = $this->cartProvider->getCart($order_type_id, $store);
      if (!$cart) {
        $cart = $this->cartProvider->createCart($order_type_id, $store);
      }
      // If the required product is not in the cart, add it to the body.
      if ($purchased_entity->hasField('field_iq_commerce_required')) {
        \Drupal::logger('iq_commerce')->notice('on the right way');
        $required_products = $purchased_entity->get('field_iq_commerce_required')->getValue();
        foreach ($required_products as $required_product) {
          if ($required_product['target_id']) {
            $required_product = ProductVariation::load($required_product['target_id']);
            $added_products++;
            $body[] = [
              'purchased_entity_type' => 'commerce_product_variation',
              'purchased_entity_id' => $required_product->id(),
              'quantity' => (!empty($order_item_data['quantity'])) ? $order_item_data['quantity'] : 1,
            ];
          }
        }
      }
    }
    // If there were any required products that were added, add them to the
    // event's body, so they can be sent to the cart API.
    if ($added_products > 0) {
      $event->setBody($body);
    }

  }

  /**
   * Selects the store for the given purchasable entity.
   *
   * If the entity is sold from one store, then that store is selected.
   * If the entity is sold from multiple stores, and the current store is
   * one of them, then that store is selected.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The entity being added to cart.
   *
   * @throws \Exception
   *   When the entity can't be purchased from the current store.
   *
   * @return \Drupal\commerce_store\Entity\StoreInterface
   *   The selected store.
   */
  protected function selectStore(PurchasableEntityInterface $entity) {
    $stores = $entity->getStores();
    if (count($stores) === 1) {
      $store = reset($stores);
    }
    elseif (count($stores) === 0) {
      // Malformed entity.
      throw new UnprocessableEntityHttpException('The given entity is not assigned to any store.');
    }
    else {
      $store = $this->currentStore->getStore();
      if (!in_array($store, $stores)) {
        // Indicates that the site listings are not filtered properly.
        throw new UnprocessableEntityHttpException("The given entity can't be purchased from the current store.");
      }
    }

    return $store;
  }
}
