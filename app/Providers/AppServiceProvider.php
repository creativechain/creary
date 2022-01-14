<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        DB::listen(function ($query) {
            Log::debug($query->sql);
        });

        $request = request();

        $langs = config('langs');
        $lang = 'en';

        $cookieLang = $request->cookie('creary_language');
        $navLang = $request->getPreferredLanguage(['en', 'es']);

        if ($cookieLang) {
            $lang = $cookieLang;
        } else if ($navLang) {
            $lang = $navLang;
        }

        //Force to use only available langs
        if (!array_key_exists($lang, $langs)) {
            $lang = 'en';
        }

        App::setLocale($lang);
        Schema::defaultStringLength(191);

    }
}
