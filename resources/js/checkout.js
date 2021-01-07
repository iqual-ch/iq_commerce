(function ($, Drupal) {

  'use strict';

  /**
   * Behaviors.
   */
  Drupal.behaviors.iq_commerce_checkout_cart = {
    attach: function (context, settings) {
      $('[data-alter-quantity] [data-increase-item-quantity]').click( function (e) {
        e.preventDefault();
        let $target = $(this).closest('[data-alter-quantity]').find('input');
        $target.val(parseInt($target.val()) + parseInt($(this).data('increase-item-quantity')));
        $target.closest('form').submit();
      });
      $('[data-alter-quantity] [data-remove-item]').click( function (e) {
        e.preventDefault();
        let $target = $(this).closest('[data-alter-quantity]').find('input');
        $target.val(0);
        $target.closest('form').submit();
      });
    }
  };
})(jQuery, Drupal);
