## Running the app

1. Clone the repo

```sh
git clone git@github.com:harisrahman/stripe-deferred-payment.git
```

2. Navigate to directory

```sh
cd stripe-deferred-payment
```

2. Install composer dependencies

```sh
composer install
```

3. Install NPM dependencies

```sh
npm i
```

4. Copy and paste .env file from some existing place or do as below

```sh
cp .env.example .env
```

6. Set artisan app key

```sh
php artisan key:generate
```

7. Build the front-end

```sh
npm run build
```

8. Start php server

```sh
php artisan serve
```

9. App will be running on [http://localhost:8000/](http://localhost:8000/).
