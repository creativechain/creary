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

-   Create/copy the `.env` file in your root and include the secrets
-   Start the development server, running the following command `php artisan serve`, by default is used the port 8000
-   For compile styles and views, run `npm run dev`. For a more easy workflow during development also you can run `npm run watch` for check instantly the changes.

### SOME REQUIREMENTs

#### Passport (Oauth)

-   Installation:

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
php artisan passport:keys
```

-   Issuing Client Credentials

    -   Normal:

                ```
                php artisan passport:client
                ```

    -   Grant Type Password

                ```bash
                php artisan passport:client --password
                ```

    -   Personal

                ```bash
                php artisan passport:client --personal
                ```

#### Search Engine with Scout

-   Installation

```bash
composer require laravel/scout
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"


```

-   Add Searcheable to your models

```php
<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'posts_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->email;
    }
}
```
