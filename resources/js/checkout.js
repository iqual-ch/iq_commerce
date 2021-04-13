(function ($, Drupal) {

  'use strict';

  /**
   * Behaviors.
   */
  Drupal.behaviors.iq_commerce_checkout_cart = {
    attach: function (context, settings) {
      $('[data-alter-quantity] input[type="number"]').attr('min', 1);
      $('[data-alter-quantity] [data-increase-item-quantity]').click( function (e) {
        e.preventDefault();
        let $target = $(this).closest('[data-alter-quantity]').find('input');
        $target.val(Math.max(parseInt($target.attr('min')), parseInt($target.val()) + parseInt($(this).data('increase-item-quantity'))));
        clearTimeout(window.cartSubmitTimeout);
        window.cartSubmitTimeout = setTimeout(function(){
          $target.closest('form').submit();
        }, 600);

      });

    }
  };
})(jQuery, Drupal);
