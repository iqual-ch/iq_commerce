# iq_commerce

## Drupal 10 installation

- Install the lenient-plugin in your project: `composer require mglaman/composer-drupal-lenient`
- Add an exception for "drupal/commerce_repeat_order" by running `composer config --merge --json extra.drupal-lenient.allowed-list '["drupal/commerce_repeat_order"]'`
- Require iq_commerce: `composer require iqual/iq_commerce:^3.0`

## DOM PDF setup

    cp public/modules/custom/iq_commerce/patches/20200918-2244-dompdf-and-drupal-entity-print.patch patches/20200918-2244-dompdf-and-drupal-entity-print.patch
    composer patch-add dompdf/dompdf '2244 - dompdf and drupal entity_print' patches/20200918-2244-dompdf-and-drupal-entity-print.patch
