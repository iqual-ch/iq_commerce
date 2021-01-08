(function ($) {

  'use strict';

  /**
   * Behaviors.
   */
  Drupal.behaviors.iq_commerce_ajax_cart = {
    getCsrfToken: function (callback) {
      $.get(Drupal.url('rest/session/token'))
        .done(function (data) {
          callback(data);
        });
    },
    addToCart: function (csrfToken, purchasedEntityType, purchasedEntityId, quantity) {
      var orderData = {
        purchased_entity_type: purchasedEntityType,
        purchased_entity_id: purchasedEntityId,
        quantity: quantity
      };
      $(document).trigger("iq-commerce-cart-add-before", [orderData]);
      $.ajax({
        url: Drupal.url('cart/add?_format=json'),
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        data: JSON.stringify([orderData]),
        success: function (orderData) {
          $(document).trigger("iq-commerce-cart-add-after", [orderData]);
        }
      });
    },

    updateItem: function (csrfToken, orderID, itemID, quantity) {
      var orderData = {
        quantity: quantity
      };
      $(document).trigger("iq-commerce-cart-update-before", [orderData]);
      $.ajax({
        url: Drupal.url('cart/' + orderID + '/items/' + itemID + '?_format=json'),
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        data: JSON.stringify(orderData),
        success: function (orderData) {
          $(document).trigger("iq-commerce-cart-update-after", [orderData]);
        }
      });
    },

    removeFromCart: function (csrfToken, orderID, itemID) {
      var orderData = {
        orderID: orderID,
        itemID: itemID
      };
      $(document).trigger("iq-commerce-cart-remove-before", [orderData]);
      $.ajax({
        url: Drupal.url('cart/' + orderID + '/items/' + itemID + '?_format=json'),
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        success: function (orderData) {
          $(document).trigger("iq-commerce-cart-remove-after", [orderData]);
        }
      });
    },

    refreshCart: function (targetSelector, additionalData = {}) {
      $.ajax({
        url: Drupal.url('cart?_format=json'),
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
        success: function (cartData) {
          Object.keys(drupalSettings.progressive_decoupler).filter(function(key){
            return drupalSettings.progressive_decoupler[key].type == 'iq_commerce_ajax_cart_block'
          }).forEach(function(blockID){
            let $blockElement = $('#' + blockID);
            let blockData = drupalSettings.progressive_decoupler[blockID];
            let $target = $blockElement.find('[data-mini-cart-content]').html('');
            let template = Twig.twig({data: blockData.template});
            let pattern = blockData.ui_pattern;

            if (cartData && cartData[0] && cartData[0].order_items.length) {
              cartData[0].order_items.forEach(function(item){

                let fieldMapper = new iq_progessive_decoupler_FieldMapper(item, blockData.field_mapping);
                let $item = $(template.render(fieldMapper.applyMappging()));

                $(document).trigger('ajax-cart-after-item-rendered[' + pattern + ']', {
                  item: $item,
                  order: {
                    order_id: item.order_id,
                    order_item_id: item.order_item_id,
                  },
                });
                $target.append($item);
              });

              $blockElement.find('[data-total-value]').text(cartData[0].total_price.formatted);
              $blockElement.find('[data-cart-content-holder]').removeClass('loading');
              $(document).trigger('ajax-cart-after-block-rendered[' + pattern + ']', $target);
            }


          });

          // add additionalData to eventTrigger
          var updateData = {
            cartData: cartData,
            additionalData: additionalData
          };
          $(document).trigger("iq-commerce-cart-refresh-after", [updateData]);
        }
      });
    },
    attach: function (context, settings) {
      $(document).trigger("iq-commerce-cart-init");
    }
  };

})(jQuery);





