(function ($, Drupal) {

  'use strict';

  /**
   * Behaviors.
   */
  Drupal.behaviors.iq_commerce_checkout_cart = {
    attach: function (context, settings) {
      $('[data-alter-quantity] input[type="number"]').attr('min', 1);
      $('[data-alter-quantity] [data-increase-item-quantity]').click(function (e) {
        e.preventDefault();
        let $target = $(this).closest('[data-alter-quantity]').find('input');
        $target.val(Math.max(parseInt($target.attr('min')), parseInt($target.val()) + parseInt($(this).data('increase-item-quantity'))));
        clearTimeout(window.cartSubmitTimeout);
        window.cartSubmitTimeout = setTimeout(function () {
          $target.closest('form').submit();
        }, 600);

      });

    }
  };

  Drupal.behaviors.paymentProviders = {
    attach: function (context, settings) {

      $(document).ready(function () {

        // Checkout Payment providers input check on image click
        const paymentProviders = $('#edit-commerce-payrexx-integration-commerce-pane-payment-method-selection-payment-methods > div:not(:first-of-type) > .js-form-item');
        paymentProviders.each(function (e) {
          let input = $(this).find('input');
          $(this).on('click', function () {
            input.prop("checked", true);
          })
        });
      });

    }
  };

})(jQuery, Drupal);
