<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('/register', 'RegistrationController@register');
$router->post('/login', 'AuthenticationController@login');
$router->post('/password/reset', 'PasswordResetController@send');
$router->post('/password/reset/{token}', 'PasswordResetController@reset');
$router->post('/email/verify/{token}', 'EmailVerificationController@verify');

$router->group(['middleware' => 'auth'], function () use($router) {
    $router->post('/logout', 'AuthenticationController@logout');
    $router->post('/refresh', 'AuthenticationController@refresh');
    $router->get('/me', 'AuthenticationController@me');
    $router->post('/email/verification/resend', 'EmailVerificationController@resend');


    $router->get('/exercises', 'ExerciseController@all');
    $router->get('/exercises/{id:\d+}', 'ExerciseController@single');
    $router->post('/exercises', 'ExerciseController@store');
    $router->put('/exercises/{id:\d+}', 'ExerciseController@update');
    $router->delete('/exercises/{id:\d+}', 'ExerciseController@destroy');
});
