(function ($) {



  $(document).on("iq-commerce-cart-add-after", function (e, orderData) {
    relatedProductsContainer = $('<div class="related-products-overlay">')[0];
    let products = {};
    orderData.related_products.forEach(function (variation) {
      if (!products[variation.product_id]) {
        products[variation.product_id] = {
          title: variation.title,
          variations: []
        };
      }
      products[variation.product_id].variations.push(variation)
    });
    ReactDOM.render(React.createElement(RelatedProducts, { items: products }), relatedProductsContainer);
    $('body').append($(relatedProductsContainer));
  });

})(jQuery);
