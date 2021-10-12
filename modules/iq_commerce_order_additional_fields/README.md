This Iq Commerce Order Additional Fields requires the single_datetime contrib module.
In order to install the single_datetime module, follow these next steps:
  1. Execute the command: composer require drupal/single_datetime

  2. Write the following line in the project's composer.json:

     "extra": { "drupal-libraries": { "datetimepicker": "https://github.com/xdan/datetimepicker/archive/2.5.20.zip" }, }

  3. Install the library datetimepicker manually or follow guide. (https://github.com/Vallic/single_datetime/blob/8.x-1.x/README.md)

  4. Copy the previous library zip and extract it in a folder public/libraries/datetimepicker

  5. Execute the command: drush en single_datetime

  6. See the CollectionDatePane for the implemented single datetime form element.
