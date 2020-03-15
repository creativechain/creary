## SOME REQUIREMENTs

### Passport (Oauth)

- Installation:
```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
php artisan passport:keys
```

- Issuing Client Credentials
    - Normal:
    ```
    php artisan passport:client
    ```
    
    - Grant Type Password
    ```
    php artisan passport:client --password
    ```
    
    - Personal
    ```
    php artisan passport:client --personal
    ```
    
### Search Engine with Scout

- Installation
```bash
composer require laravel/scout
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"


```

- Add Searcheable to your models
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