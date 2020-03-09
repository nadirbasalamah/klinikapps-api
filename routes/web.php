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

//For User
$router->get('/','UserController@index');
$router->post('/registerUser','UserController@registerUser');
$router->post('/loginUser','UserController@loginUser');
$router->post('/forgetPassword','UserController@forgetPassword');
$router->post('/editProfile/{id}','UserController@editProfile');
$router->post('/changePassword/{id}','UserController@changePassword');
$router->get('/user/{id}','UserController@getUserById');
$router->get('/userNutRecord/{id}','UserController@getNutritionRecordById');

// //For Admin
$router->get('/patients','AdminController@getAllPatients');
$router->get('/patient/{id}','AdminController@getPatientById');
$router->post('/deletePatient/{id}','AdminController@deletePatient');
$router->post('/editPatient/{id}','AdminController@editPatient');
$router->post('/addPatient','AdminController@addPatient');
$router->get('/getPatient/{fullname}','AdminController@getPatientByName');

$router->get('/nutritionists','AdminController@getAllNutritionists');
$router->get('/nutritionist/{id}','AdminController@getNutritionistById');
$router->post('/deleteNutritionist/{id}','AdminController@deleteNutritionist');
$router->post('/editNutritionist/{id}','AdminController@editNutritionist');
$router->post('/addNutritionist','AdminController@addNutritionist');
$router->get('/getNutritionist/{fullname}','AdminController@getNutritionistByName');

// //For Doctor
$router->post('/setNutritionist/{id}','DoctorController@setNutritionist');
$router->post('/updateAntropometry/{id}','DoctorController@updateAntropometry');
$router->post('/updateBiochemistry/{id}','DoctorController@updateBiochemistry');
$router->post('/updateClinic/{id}','DoctorController@updateClinic');
$router->post('/updateDietary/{id}','DoctorController@updateDietary');
$router->post('/updateDiagnose/{id}','DoctorController@updateDiagnose');
$router->post('/updateInterenvention/{id}','DoctorController@updateInterenvention');
$router->post('/updateMonitoring/{id}','DoctorController@updateMonitoring');
$router->get('/nutRecord/{id}','DoctorController@getNutritionRecordById');

// //For Articles
$router->get('/articles','ArticleController@getAllArticles');
$router->get('/guides','ArticleController@getAllGuides');
$router->get('/article/{id}','ArticleController@getArticleById');
$router->get('/getArticle/{title}','ArticleController@getArticleByTitle');
$router->post('/addArticle','ArticleController@addArticle');
$router->post('/deleteArticle/{id}','ArticleController@deleteArticle');
$router->post('/editArticle/{id}','ArticleController@editArticle');