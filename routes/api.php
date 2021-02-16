<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('/register', 'RegistrationController@register');
$router->post('/login', 'AuthenticationController@login');
$router->post('/password/reset', 'PasswordResetController@send');
$router->post('/password/reset/{token}', 'PasswordResetController@reset');

$router->group(['middleware' => 'auth'], function () use($router) {
    $router->post('/logout', 'AuthenticationController@logout');
    $router->post('/refresh', 'AuthenticationController@refresh');
    $router->get('/me', 'AuthenticationController@me');
});
