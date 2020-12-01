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
    updateCart: function (targetSelector, additionalData = {}) {
      $.ajax({
        url: Drupal.url('cart?_format=json'),
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
        success: function (cartData) {
          const domContainer = document.querySelector(targetSelector);
          ReactDOM.render(e(MiniCart, {items: cartData[0].order_items}), domContainer);

          // add additionalData to eventTrigger
          var updateData = {
            cartData: cartData,
            additionalData: additionalData
          };
          $(document).trigger("iq-commerce-cart-update-after", [updateData]);
        }
      });
    },
    attach: function (context, settings) {
      $(document).trigger("iq-commerce-cart-init");
    }
  };

})(jQuery);





