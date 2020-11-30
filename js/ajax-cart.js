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
    addToCart: function (csrfToken, purchasedEntityType, purchasedEntityId, qty) {
      $('body').append('<div class="add-to-cart-ajax-throbber ajax-progress ajax-progress-fullscreen"></div>');
      $.ajax({
        url: Drupal.url('cart/add?_format=json'),
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        data: JSON.stringify([{
          purchased_entity_type: purchasedEntityType,
          purchased_entity_id: purchasedEntityId,
          quantity: qty
        }]),
        success: function (data) {
          // Product was added to cart.
          // trigger the events
          var orderItem = data[0];
          var $overlay = $('#add-to-cart-overlay');
          /*$overlay.find('.purchased-entity').text(orderItem.title);
          $overlay.foundation('open');*/
          Drupal.behaviors.iq_commerce_ajax_cart.updateCart();
          $('.add-to-cart-ajax-throbber').remove();
        }
      });
    },
    updateCart: function () {
      // Adjust this to our cart block

      // var $cartCount = $('.store-action--cart .store-action__link__count');
      // if ($cartCount.length) {
        $.ajax({
          url: Drupal.url('cart?_format=json'),
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
          success: function (data) {
        console.log(data);
            const domContainer = document.querySelector('[data-mini-cart-content]');
            ReactDOM.render(e(MiniCart), domContainer);
          }
        });
      // }
    },
    attach: function (context, settings) {
      $(context).find('.add-to-cart-link').once('add-to-cart-link-init').each(function () {
        $(this).on('click', function (e) {
          e.preventDefault();
          var variationId = $(this).data('variation');
          Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
            Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, 'commerce_product_variation', variationId, 1);
          });
        });
      });

      $(context).find('form.commerce-variation-add-to-cart-form').once('add-to-cart-form-init').each(function () {
        $(this).on('click', '.form-submit', function (e) {
          $(this).parents('form').data('button-clicked', 'cart');
        });
        $(this).on('submit', function (e) {
          e.preventDefault();

          let formData = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value;
            return obj;
          }, {});

          Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
            Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, formData['variation_type'], parseInt(formData['variation_id']), parseInt(formData['quantity']));
          });
        });
      });
    }
  };

})(jQuery);
