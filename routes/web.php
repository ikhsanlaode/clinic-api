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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login','AuthController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/logout','AuthController@logout');
    
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('/','UserController@index');
        $router->get('/{id}','UserController@show');
        $router->post('/','UserController@store');
        $router->put('/','UserController@update');
        $router->delete('/','UserController@delete');
    });

    $router->group(['prefix' => 'role'], function () use ($router) {
        $router->get('/','RoleController@index');
        $router->get('/{id}','RoleController@show');
        $router->post('/','RoleController@store');
        $router->put('/','RoleController@update');
        $router->delete('/','RoleController@delete');
    });

    $router->group(['prefix' => 'medic'], function () use ($router) {
        $router->group(['prefix' => 'history'], function () use ($router) {
            $router->get('/','MedicalHistoryController@index');
            $router->get('/{id}','MedicalHistoryController@show');
            $router->post('/','MedicalHistoryController@store');
            $router->put('/','MedicalHistoryController@update');
            $router->delete('/','MedicalHistoryController@delete');
        });

        $router->group(['prefix' => 'treatment'], function () use ($router) {
            $router->get('/','TreatmentController@index');
            $router->get('/{id}','TreatmentController@show');
            $router->post('/','TreatmentController@store');
            $router->put('/','TreatmentController@update');
            $router->delete('/','TreatmentController@delete');
        });
    });

});