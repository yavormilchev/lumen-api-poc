<?php

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

$router->group(['prefix' => 'api/reviews'], function () use ($router) {
    $router->get('/', ['as' => 'review.index', 'uses' => 'ReviewController@index']);

    $router->post('/', ['as' => 'review.create', 'uses' => 'ReviewController@create']);

    $router->get('/{id:[0-9]+}', ['as' => 'review.view', 'uses' => 'ReviewController@view']);

    $router->put('/{id:[0-9]+}', ['as' => 'review.update', 'uses' => 'ReviewController@update']);

    $router->delete('/{id:[0-9]+}', ['as' => 'review.delete', 'uses' => 'ReviewController@delete']);
});
