<?php

$router->get('/posts', 'PostDoctrineController@index');
$router->get('/post/{id}', 'PostDoctrineController@show');
$router->post('/post', 'PostDoctrineController@store');
$router->put('/post/{id}', 'PostDoctrineController@update');
$router->delete('/post/{id}', 'PostDoctrineController@destroy');

$router->group(
    ['prefix' => 'auth'], function ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');

});