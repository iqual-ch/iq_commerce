(function ($) {
  $(document).on("iq-commerce-cart-add-after", function (e, orderData) {
    if (orderData.related_products.length) {
      Object.keys(drupalSettings.progressive_decoupler).filter(function(key){
        return drupalSettings.progressive_decoupler[key].type == 'iq_commerce_related_product_block'
      }).forEach(function(blockID){

        let $blockElement = $('#' + blockID);

        if ($blockElement.find('[data-related-product-overlay]').hasClass('hidden')) {

          let blockData = drupalSettings.progressive_decoupler[blockID];
          let products = {};
          let $target = $blockElement.find('[data-related-product-items]').html('');
          let template = Twig.twig({data: blockData.template});
          let pattern = blockData.ui_pattern;

          orderData.related_products.forEach(function (variation) {
            if (!products[variation.product_id.product_id]) {
              let product = $.extend({}, variation.product_id);
              product.variations = [];
              products[variation.product_id.product_id] = product;
            }
            products[variation.product_id.product_id].variations.push(variation)
          });

          Object.keys(products).forEach(function(product_id){
            let fieldMapper = new iq_progessive_decoupler_FieldMapper(products[product_id], blockData.field_mapping);
            let $item = $(template.render(fieldMapper.applyMappging()));
            $(document).trigger('related-product-item-rendered[' + pattern + ']', {
              item: $item
            });
            $target.append($item);
          });

          $blockElement.find('[data-related-product-overlay]').removeClass('hidden');
        }
      });
    }
  });

  $(document).ready(function(){
    $('[data-related-product-close]').click(function(){
      $(this).closest('[data-related-product-overlay]').addClass('hidden');
    });
  });

})(jQuery);
