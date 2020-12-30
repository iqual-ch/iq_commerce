<?php

namespace Drupal\iq_commerce\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("alter_cart_quantity_field")
 */
class AlterCartQuantityField extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options.
   *
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['btn_delete_label'] = ['default' => $this->t('Remove')];

    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['btn_delete_label'] = [
      '#title' => $this->t('Tooltip remove button'),
      '#type' => 'textfield',
      '#default_value' => $this->options['btn_delete_label'],

    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {

    return [
      '#type' => 'inline_template',
      '#template' => '<button data-drupal-selector="edit-remove-button-{{ id }}" type="submit" id="edit-remove-button-{{ id }}" name="delete-order-item-{{ id }}" title="{{name}}"><i class="far fa-trash-alt"></i></button>',
      '#remove_order_item' => TRUE,
      '#row_index' => $values->index,
      '#context' => [
        'id'  => $values->index,
        'name' => $this->options['btn_delete_label'],
      ],
    ];

  }

}
