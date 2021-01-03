(function ($, Drupal) {

  $(document).ready(function () {

    $('.product-information').find('input[name*="quantity"]').attr('step', 1);

    $('.iq-image-slider').each(function () {

      var config = {
        dots: false,
        nav: false,
        items: 1,
        loop: true,
        margin: 15
      };

      if ($(this).data('navigation')) {
        config.dotClass = $(this).data('navigation');
        config.dots = true;
        if ($(this).data('speed')) {
          config.dotsSpeed = $(this).data('speed') * 1000;
        }
      }

      if ($(this).data('arrow-left') && $(this).data('arrow-right')) {
        config.navText = ["<i class=\"" + $(this).data('arrow-left') + "\"></i>", "<i class=\"" + $(this).data('arrow-right') + "\"></i>"];
        config.nav = true;
        if ($(this).data('speed')) {
          config.navSpeed = $(this).data('speed') * 1000;
        }
      }

      $(this).addClass('owl-carousel');
      $(this).owlCarousel(config);
    });
  })

})(jQuery, Drupal);
