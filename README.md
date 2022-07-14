## Steps To Install

- clone this repository
- `cd simple-wallet`
- run `composer install` (make sure composer & php 8 is installed)
- run `touch database/database.sqlite` to make an empty sqlite database
- copy .env.example file to .env file
- run `php artisan migrate`
- run `php artisan serve`
- open another terminal tab
- run `php artisan queue:work`
