<?php

//$router->get('/users', 'UserController@index');
//
//$router->get('/posts', 'PostController@index');
//$router->post('/post', 'PostController@store');
//$router->put('/post/{id}', 'PostController@update');
//$router->delete('/post/{id}', 'PostController@destroy');

$router->get('/posts', 'PostDoctrineController@index');
$router->post('/post', 'PostDoctrineController@store');
$router->put('/post/{id}', 'PostDoctrineController@update');
$router->delete('/post/{id}', 'PostDoctrineController@destroy');