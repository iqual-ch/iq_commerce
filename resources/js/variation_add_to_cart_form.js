(function ($) {
  Drupal.behaviors.iq_commerce_variation_add_to_cart_form = {
    attach: function (context, settings) {

      $(context).find('button.plus').on('click', function () {
        const $input = $(this).siblings('input[type="number"].quantity');
        $input.val(Math.max(0, parseInt($input.val()) + 1));
      })

      $(context).find('button.minus').on('click', function () {
        const $input = $(this).siblings('input[type="number"].quantity');
        $input.val(Math.max(0, parseInt($input.val()) - 1));
      });

    }
  };
})(jQuery);