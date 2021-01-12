# Creary

## First steps

Recommended versions:

-   PHP: 7.3 or superior
-   Composer: 2.0.8
-   Node: 12

The project uses PHP extensions for MySQL and MongoDB, before installing the dependencies check first if you have activated the extensions on the `php.ini` file.

For install all the dependencies run the following scripts:

```bash
composer install
npm install
```

## Daily workflow

-   Create/copy the `.env` file in the root folder and include the secrets
-   Start the development server, running the following command `php artisan serve`, by default is used the port 8000
-   For compile styles and views, run `npm run dev`. For a more easy workflow during development also you can run `npm run watch` for check instantly the changes.
