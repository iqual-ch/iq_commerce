(function ($) {
  Drupal.behaviors.iq_commerce_variation_add_to_cart_form = {
    attach: function (context, settings) {

      $(context).find('.commerce-variation-add-to-cart__quantity-button--plus').on('click', function () {
        const $input = $(this).siblings('input[type="number"].commerce-variation-add-to-cart__quantity-input');
        $input.val(Math.max(0, parseInt($input.val()) + 1));
      })

      $(context).find('.commerce-variation-add-to-cart__quantity-button--minus').on('click', function () {
        const $input = $(this).siblings('input[type="number"].commerce-variation-add-to-cart__quantity-input');
        $input.val(Math.max(0, parseInt($input.val()) - 1));
      });

    }
  };
})(jQuery);