(function ($) {
  $(document).on("related-product-item-rendered[iq-commerce-related-product-item]", function (e, args) {

    // Update variation price
    args.item.find('select').change(function () {
      $(this).siblings('[data-variation-price-target]').text($(this).find(":selected").data('variation-price'))
    });

    // Add to cart
    args.item.find('form').submit(function (e) {
      e.preventDefault();

      let formData = new FormData(this);
      Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
        Drupal.behaviors.iq_commerce_ajax_cart.addToCart(csrfToken, 'commerce_product_variation', parseInt(formData.get('variation_id')), 1);
      });

      $(this).find('button').text($(this).find('button').data('text-success')).attr('disabled', true);
      $(this).closest('[data-variations]').find('[data-add-more]').removeClass('hidden');
    });

    // Update variation price
    args.item.find('[data-add-more]').click(function () {
      let $element = $(this).parent().find('[data-variation]').last().clone().insertBefore($(this));
      $element.find('button').removeAttr('disabled');
      $element.find('[data-variation-price-target]').text('');
      $(this).addClass('hidden');
      $(document).trigger('related-product-item-rendered[iq-commerce-related-product-item]', {
        item: $element
      });
    });
  });

})(jQuery);
