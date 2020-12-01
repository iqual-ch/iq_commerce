(function ($) {

  $(document).on("iq-commerce-cart-init", function (e) {

    // load cart on page load
    Drupal.behaviors.iq_commerce_ajax_cart.updateCart('[data-mini-cart-content]', {
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
          Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, orderProductData['variation_type'], parseInt(orderProductData['variation_id']), parseInt(orderProductData['quantity']));
        });
      });
    });
  });

  $(document).on("iq-commerce-cart-add-before", function (e, orderData) {
    console.log(orderData);
  });

  $(document).on("iq-commerce-cart-add-after", function (e, orderData) {
    console.log(orderData);
    Drupal.behaviors.iq_commerce_ajax_cart.updateCart('[data-mini-cart-content]', {
      showCart: true,
    });
  });

  $(document).on("iq-commerce-cart-update-after", function (e, updateData) {
    if (updateData.additionalData.showCart) {
      $('.iq-commerce-mini-cart').addClass('show')
    }

    let totalQuantity =  updateData.cartData[0].order_items.map(function(item){
      return Math.round(item.quantity)
    }).reduce(function(a,b){
        return a + b;
    });

    $('.iq-commerce-mini-cart .count').text(totalQuantity);

  });

  $('.iq-commerce-mini-cart').mouseout(function(){
    $(this).removeClass('show')
  })





})(jQuery);
