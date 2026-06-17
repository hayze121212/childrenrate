<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// OAuth2 — выдача токена
$routes->post('oauth/token', 'OAuthController::token');
$routes->options('oauth/token', 'OAuthController::token');

// Рейтинги (CRUD, требует Bearer-токен)
$routes->get('RatingApi/rating', 'RatingApi::rating');
$routes->post('RatingApi/rating', 'RatingApi::rating');
$routes->options('RatingApi/rating', 'RatingApi::rating');
$routes->post('RatingApi/store', 'RatingApi::store');
$routes->options('RatingApi/store', 'RatingApi::store');
$routes->post('RatingApi/update/(:num)', 'RatingApi::update/$1');
$routes->options('RatingApi/update/(:num)', 'RatingApi::update/$1');
$routes->delete('RatingApi/delete/(:num)', 'RatingApi::delete/$1');
$routes->options('RatingApi/delete/(:num)', 'RatingApi::delete/$1');

// Текущий пользователь по токену
$routes->get('api/user', 'ApiController::user');
$routes->options('api/user', 'ApiController::user');
