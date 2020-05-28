<?php

$lang = file_get_contents(storage_path('app/isolangs.json'));
$lang = json_decode($lang, true);

return $lang;
