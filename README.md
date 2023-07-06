# iq_commerce

## DOM PDF setup

    cp public/modules/custom/iq_commerce/patches/20200918-2244-dompdf-and-drupal-entity-print.patch patches/20200918-2244-dompdf-and-drupal-entity-print.patch
    composer patch-add dompdf/dompdf '2244 - dompdf and drupal entity_print' patches/20200918-2244-dompdf-and-drupal-entity-print.patch
