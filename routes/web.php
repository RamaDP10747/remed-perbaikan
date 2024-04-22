<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/stuffs', 'StuffController@index');

$router->post('/login', 'UserController@login');
$router->get('/logout', 'UserController@logout');

$router->group(['prefix' => 'stuff'], function() use ($router){
    
    $router->get('/data', 'StuffController@index');
    $router->post('/', 'StuffController@store');
    $router->get('/trash', 'StuffController@trash');

    $router->get('{id}', 'StuffController@show');
    $router->patch('/{id}', 'StuffController@update');
    $router->delete('/{id}', 'StuffController@destroy');
    $router->get('/restore/{id}', 'StuffController@restore');
    $router->delete('/permanent/{id}', 'StuffController@deletePermanent');
});

$router->group(['prefix' => 'user'], function() use ($router) {
    // static routes : tetap
    $router->post('/store', 'UserController@store');
    $router->get('/trash', 'UserController@trash');

    //dunamic routes : berubah - rubah
    $router->get('{id}', 'UserController@show');
    $router->patch('/{id}', 'UserController@update');
    $router->delete('/{id}','UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->delete('/permanent/{id}', 'UserController@deletePermanent');
});

$router->group(['prefix' => 'inbound-stuffs/', 'middleware' => 'auth'], function() use ($router) {
  
    $router->post('store', 'InboundStuffsController@store');
    $router->get('/', 'InboundStuffsController@index');
    $router->get('detail/{id}', 'InboundStuffsController@show');
    $router->patch('update/{id}', 'InboundStuffsController@update');
    $router->delete('delete/{id}','InboundStuffsController@destroy');
    $router->delete('recyle-bin','InboundStuffsController@recyleBin');
    $router->get('/restore/{id}', 'InboundStuffsController@restore');
    $router->delete('/force-delete/{id}', 'InboundStuffsController@forceDestroy');
});