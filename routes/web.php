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

$router->get('/', function () use($router) {
    return $router->app->version();
});

$router->post('/login', 'userController@login');
$router->get('/logout', 'userController@logout');

// router stuff
$router->group(['prefix' => '/stuff', 'middleware' => 'auth'], function() use($router) {
    // static routes
    //  Router statis menangani URL yang memiliki pola tetap dan tidak berubah
     $router->get('/data', 'StuffController@index'); 
     $router->post('/', 'StuffController@store'); 
     $router->get('/trash', 'StuffController@trash'); 
 
    //dynamic routes
    //  Router dinamis menangani URL yang dapat berubah sesuai dengan parameter atau variabel 
    // tertentu yang diberikan
     $router->get('{id}', 'StuffController@show'); 
     $router->patch('{id}', 'StuffController@update'); 
     $router->delete('{id}','StuffController@destroy'); 
     $router->get('/trash', 'StuffController@trash'); 
     $router->get('/restore/{id}', 'StuffController@restore'); 
     $router->get('/permanent/{id}', 'StuffController@deletePermanent'); 
});

// router user
$router->group(['prefix' => '/user', 'middleware' => 'auth'], function() use ($router) {
    // static routes
    $router->get('/data', 'UserController@index');
    $router->get('/trash', 'UserController@trash');
    $router->post('/', 'UserController@store');

    // dynamic routes
    $router->get('{id}', 'UserController@show');
    $router->patch('{id}', 'UserController@update');
    $router->delete('{id}', 'UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->get('/permanent/{id}', 'UserController@deletePermanent');
});


// router inboundStuff
$router->group(['prefix' => 'inbound-stuff/', 'middleware' => 'auth'], function() use ($router) {
    // static routes
    $router->post('/store', 'InboundStuffController@store');
    $router->get('/', 'InboundStuffController@index');
    // $router->get('trash', 'InboundStuffController@trash');


    // dynamic routes
    $router->get('detail/{id}', 'InboundStuffController@show');
    $router->patch('update/{id}', 'InboundStuffController@update');
    // $router->delete('delete/{id}', 'InboundStuffController@destroy');
    // $router->get('restore/{id}', 'InboundStuffController@restore');
    // $router->get('deletePermanent/{id}', 'InboundStuffController@deletePermanent');
});

// router stuffStock
$router->group(['prefix' => 'stuff-Stock', 'middleware' => 'auth'], function () use ($router) {
    // static routes
    $router->get('/', 'StuffStockController@index');
    $router->get('trash', 'StuffStockController@trash');

    // dynamic routes
    $router->delete('delete/{id}', 'StuffStockController@destroy');
    $router->get('deletePermanent/{id}', 'StuffStockController@deletePermanent');
    $router->get('restore/{id}', 'StuffController@restore');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
    $router->post('sub-stock/{id}', 'StuffStockController@subStock');
});