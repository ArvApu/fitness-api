<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('/login', 'AuthenticationController@login');
$router->post('/refresh', 'AuthenticationController@refresh');

$router->post('/register', 'RegistrationController@register');

$router->post('/password/reset', 'PasswordResetController@send');
$router->post('/password/reset/{token}', 'PasswordResetController@reset');

$router->post('/email/verify/{token}', 'EmailVerificationController@verify');

$router->post('/users/invite/{token}', 'UserInvitationController@confirm');

$router->group(['middleware' => 'auth'], function () use($router) {
    $router->get('/broadcasting/auth', 'BroadcastController@authenticate');
    $router->post('/broadcasting/auth', 'BroadcastController@authenticate');

    $router->get('/me', 'AuthenticationController@me');
    $router->post('/logout', 'AuthenticationController@logout');

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

    $router->get('/events', 'EventController@all');
    $router->get('/events/{id:\d+}', 'EventController@single');
    $router->get('/events/export', 'EventController@export');
    $router->post('/events', 'EventController@store');
    $router->put('/events/{id:\d+}', 'EventController@update');
    $router->delete('/events/{id:\d+}', 'EventController@destroy');

    $router->get('/messages/{id:\d+}', 'MessageController@getByUser');
    $router->post('/messages/{to:\d+}', 'MessageController@send');

    $router->get('/users', 'UserController@all');

    $router->post('/users/invite', 'UserInvitationController@invite');

    $router->get('/workouts/logs', 'WorkoutLogController@all');
    $router->get('/workouts/logs/{id:\d+}', 'WorkoutLogController@single');
    $router->post('/workouts/logs', 'WorkoutLogController@store');
    $router->delete('/workouts/logs/{id:\d+}', 'WorkoutLogController@destroy');

    $router->get('/exercises/logs', 'ExerciseLogController@all');
    $router->get('/exercises/logs/{id:\d+}', 'ExerciseLogController@single');
});
