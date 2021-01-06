(function ($) {

  $(document).on("iq-commerce-cart-init", function (e) {

    // load cart on page load
    Drupal.behaviors.iq_commerce_ajax_cart.refreshCart('[data-mini-cart-content]', {
      showCart: false,
    });

    // bind cart update to forms
    $('form.commerce-variation-add-to-cart-form').once('add-to-cart-form-init').each(function () {
      $(this).on('click', '.form-submit', function (e) {
        $(this).parents('form').data('button-clicked', 'cart');
      });
      $(this).on('submit', function (e) {
        e.preventDefault();
        var orderProductData = $(this).serializeArray().reduce(function (obj, item) {
          obj[item.name] = item.value;
          return obj;
        }, {});
        Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
          Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, 'commerce_product_variation', parseInt(orderProductData['variation_id']), parseInt(orderProductData['quantity']));
        });
      });
    });
  });

    // bind cart update to forms - product
    $('form.commerce-order-item-add-to-cart-form').once('add-to-cart-form-init').each(function () {
      $(this).on('click', '.form-submit', function (e) {
        $(this).parents('form').data('button-clicked', 'cart');
      });
      $(this).on('submit', function (e) {
        e.preventDefault();

        var orderProductData = {
          'variation_id' : $(this).attr('action').split('v=')[1],
          'quantity' : $(this).find('input[name*="quantity"]').val()
        }

        Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
          Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, 'commerce_product_variation', parseInt(orderProductData['variation_id']), parseInt(orderProductData['quantity']));
        });
      });
    });


  $(document).on("iq-commerce-cart-add-before", function (e, orderData) {

  });

  $(document).on("iq-commerce-cart-add-after", function (e, orderData) {
    Drupal.behaviors.iq_commerce_ajax_cart.refreshCart('[data-mini-cart-content]', {
      showCart: true,
    });
  });

  $(document).on("iq-commerce-cart-refresh-after", function (e, updateData) {
    if (updateData.additionalData.showCart) {
      $('.iq-commerce-mini-cart').addClass('show')
    }

    let totalQuantity = 0

    if (updateData.cartData[0].order_items.length) {
      totalQuantity = updateData.cartData[0].order_items.map(function (item) {
        return Math.round(item.quantity)
      }).reduce(function (a, b) {
        return a + b;
      });
    }

    $('.iq-commerce-mini-cart .count').text(totalQuantity);

  });

  $(document).on("iq-commerce-cart-remove-after", function (e, orderData) {
    Drupal.behaviors.iq_commerce_ajax_cart.refreshCart('[data-mini-cart-content]', {
      showCart: true,
    });
  });

  $(document).on("iq-commerce-cart-update-after", function (e, orderData) {
    Drupal.behaviors.iq_commerce_ajax_cart.refreshCart('[data-mini-cart-content]', {
      showCart: true,
    });
  });


  $('.iq-commerce-mini-cart').mouseout(function () {
    $(this).removeClass('show')
  })

  $(document).on("iq-commerce-cart-update-after", function (e, orderData) {
    Drupal.behaviors.iq_commerce_ajax_cart.refreshCart('[data-mini-cart-content]', {
      showCart: true,
    });
  });




})(jQuery);
