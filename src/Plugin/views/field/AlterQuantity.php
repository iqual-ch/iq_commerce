<?php

namespace Drupal\iq_commerce\Plugin\views\field;

use Drupal\commerce_cart\Plugin\views\field\EditQuantity;
use Drupal\Core\Form\FormStateInterface;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("commerce_order_item_alter_quantity")
 */
class AlterQuantity extends EditQuantity {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['add_quantity_button'] = ['default' => FALSE];
    $options['add_remove_button'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['add_quantity_button'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add buttons to increase/decrease quantity'),
      '#default_value' => $this->options['add_quantity_button'],
    ];

    $form['add_remove_button'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add button to remove item'),
      '#default_value' => $this->options['add_remove_button'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    parent::viewsForm($form, $form_state);

    if ($this->options['add_quantity_button'] || $this->options['add_remove_button']) {
      foreach ($form['edit_quantity'] as $key => $input) {
        $form['edit_quantity'][$key] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['item-update'],
            'data-alter-quantity' => ''
          ]
        ];

        if ($this->options['add_quantity_button']) {
          $form['edit_quantity'][$key]['quantity-decrease'] = [
            '#type' => 'inline_template',
            '#template' => '<button title="{{ label }}" aria-label="{{ label }}" class="remove-one" data-increase-item-quantity="-1">-</button>',
            '#context'  => [
              'label' => $this->t("Quantity -1"),
            ],
          ];
        }

        $form['edit_quantity'][$key]['quantity-edit'] = $input;

        if ($this->options['add_quantity_button']) {
          $form['edit_quantity'][$key]['quantity-increase'] = [
            '#type' => 'inline_template',
            '#template' => '<button title="{{ label }}" aria-label="{{ label }}" class="add-one" data-increase-item-quantity="1">+</button>',
            '#context'  => [
              'label' => $this->t("Quantity +1"),
            ],
          ];
        }

        if ($this->options['add_remove_button']) {
          $form['edit_quantity'][$key]['remove-item'] = [
            '#type' => 'inline_template',
            '#template' => '<button title="{{ label }}" aria-label="{{ label }}" class="remove-all" data-remove-item><i class="fas fa-trash-alt"></i></button>',
            '#context'  => [
              'label' => $this->t("Remove item"),
            ],
          ];
        }
      }

      $form['actions']['submit']['#show_update_message'] = FALSE;
    }
  }

  /**
   * Submit handler for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if (empty($triggering_element['#update_cart'])) {
      // Don't run when the "Remove" or "Empty cart" buttons are pressed.
      return;
    }

    $order_storage = $this->entityTypeManager->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $cart */
    $cart = $order_storage->load($this->view->argument['order_id']->getValue());
    $quantities = $form_state->getValue($this->options['id'], []);
    $save_cart = FALSE;
    foreach ($quantities as $row_index => $quantity) {
      if (!empty($quantity['quantity-edit'])) {
        $quantity = $quantity['quantity-edit'];
      }
      if (!is_numeric($quantity) || $quantity < 0) {
        // The input might be invalid if the #required or #min attributes
        // were removed by an alter hook.
        continue;
      }
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $this->getEntity($this->view->result[$row_index]);
      if ($order_item->getQuantity() == $quantity) {
        // The quantity hasn't changed.
        continue;
      }

      if ($quantity > 0) {
        $order_item->setQuantity($quantity);
        $this->cartManager->updateOrderItem($cart, $order_item, FALSE);
      }
      else {
        // Treat quantity "0" as a request for deletion.
        $this->cartManager->removeOrderItem($cart, $order_item, FALSE);
      }
      $save_cart = TRUE;
    }

    if ($save_cart) {
      $cart->save();
      if (!empty($triggering_element['#show_update_message'])) {
        $this->messenger->addMessage($this->t('Your shopping cart has been updated.'));
      }
    }
  }


}
