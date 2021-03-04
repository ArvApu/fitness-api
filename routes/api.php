<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('/register', 'RegistrationController@register');
$router->post('/login', 'AuthenticationController@login');
$router->post('/password/reset', 'PasswordResetController@send');
$router->post('/password/reset/{token}', 'PasswordResetController@reset');
$router->post('/email/verify/{token}', 'EmailVerificationController@verify');

$router->group(['middleware' => 'auth'], function () use($router) {
    $router->get('/broadcasting/auth', 'BroadcastController@authenticate');
    $router->post('/broadcasting/auth', 'BroadcastController@authenticate');

    $router->post('/logout', 'AuthenticationController@logout');
    $router->post('/refresh', 'AuthenticationController@refresh');
    $router->get('/me', 'AuthenticationController@me');
    $router->post('/email/verification/resend', 'EmailVerificationController@resend');

    $router->get('/exercises', 'ExerciseController@all');
    $router->get('/exercises/{id:\d+}', 'ExerciseController@single');
    $router->post('/exercises', 'ExerciseController@store');
    $router->put('/exercises/{id:\d+}', 'ExerciseController@update');
    $router->delete('/exercises/{id:\d+}', 'ExerciseController@destroy');

    $router->get('/workouts', 'WorkoutController@all');
    $router->get('/workouts/{id:\d+}', 'WorkoutController@single');
    $router->post('/workouts', 'WorkoutController@store');
    $router->post('/workouts/{id:\d+}/exercises', 'WorkoutController@assignExercises');
    $router->put('/workouts/{id:\d+}', 'WorkoutController@update');
    $router->delete('/workouts/{id:\d+}', 'WorkoutController@destroy');

    $router->get('/days', 'DayController@all');
    $router->get('/days/{id:\d+}', 'DayController@single');
    $router->post('/days', 'DayController@store');
    $router->put('/days/{id:\d+}', 'DayController@update');
    $router->delete('/days/{id:\d+}', 'DayController@destroy');

    $router->get('/messages/{id:\d+}', 'MessageController@getByUser');
    $router->post('/messages/{to:\d+}', 'MessageController@send');

    $router->get('/users', 'UserController@all');
});
