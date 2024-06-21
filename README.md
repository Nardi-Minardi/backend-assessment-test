## Backend Assessment Test Minardi
### Setup and install
1. Checkout a new feature branch from `main` branch or clone the repository
2. run `composer install`
3. copy `.env.example` to `.env` and update the database configuration
4. run `php artisan key:generate`
5. run `php artisan storage:link`
6. run `php artisan migrate`
7. run `php artisan db:seed`
8. run `php artisan passport:install`
9. run `php artisan passport:client --personal`
10. run `php artisan test --filter DebitCardControllerTest`
11. run `php artisan test --filter DebitCardTransactionControllerTest`
12. run `php artisan serve`

### Note
To import the postman collection, you can import all the collection from folder `minardi_collection`
