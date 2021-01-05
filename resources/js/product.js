(function ($, Drupal) {



  'use strict';

  /**
   * Behaviors.
   */
  Drupal.behaviors.iq_commerce_shop = {
    attach: function (context, settings) {

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

        $(this).trigger('destroy.owl.carousel')
        $(this).html('');

        // load images
        var $target = $(this);

        $(this).siblings('[data-img-src]').find('.field__item img').each(function () {
          let url = $(this).attr('src');
          let $a = $('<a href="' + url + '" data-gallery="product-img" data-max-width="1920" >');
          $a.append($(this).clone());
          $target.append($('<div class="iq-image">').append($a));
        });


        $(this).siblings('[data-img-src]').find('.iq-image').each(function () {
          $(this).find('a').attr('data-gallery', 'product-img');
          $target.append($(this).clone());
        });

        $target.find('a[data-gallery="product-img"]').click(function (e) {
          e.preventDefault();
          $(this).ekkoLightbox();
        });


        $(this).addClass('owl-carousel');
        $(this).owlCarousel(config);
      });


    }
  };
})(jQuery, Drupal);
