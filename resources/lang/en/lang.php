<?php

$lang = \Illuminate\Support\Facades\Storage::disk('local')->get('translations/en/lang.json');
$lang = json_decode($lang);

return $lang;
