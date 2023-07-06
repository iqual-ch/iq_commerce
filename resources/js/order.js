(function ($, Drupal) {

  /**
   * Back button on order detail pages with fallback
   */
  const $window = $(window);
  const $trigger = $('.order-detail-back-button-container > p');
  const fallback = 'https://commerce-drpl.docker-dev.iqual.ch/de/user/orders';
  let hasHistory = false;

  $window.on('beforeunload', function () {
    hasHistory = true;
  });

  $trigger.on('click', function () {
    console.log('ok?')

    window.history.go(-1);

    setTimeout(function () {
      if (!hasHistory) {
        window.location.href = fallback;
      }
    }, 1000);

    return false;
  });

})(jQuery, Drupal);
