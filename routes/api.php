<?php

Route::get('/sentry-test', function () {
    throw new Exception('Sentry is working correctly!');
});
