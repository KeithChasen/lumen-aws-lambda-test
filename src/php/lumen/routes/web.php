<?php

$router->get('/users', 'UserController@index');

$router->get('/posts', 'PostController@index');
$router->post('/post', 'PostController@store');
$router->put('/post/{id}', 'PostController@update');
$router->delete('/post/{id}', 'PostController@destroy');