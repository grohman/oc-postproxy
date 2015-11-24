<?php

Route::post('_idesigning/postproxy/subscribe/{rubric}', 'IDesigning\PostProxy\Controllers\Processing@postSubscribe');
Route::get('_idesigning/postproxy/unsubscribe', 'IDesigning\PostProxy\Controllers\Processing@getUnsubscribe');