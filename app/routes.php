<?php
/**
 * ═══════════════════════════════════════════════════
 * Application Routes
 * ═══════════════════════════════════════════════════
 */

// User Module Routes
$this->get('/users', 'UserController', 'index');         // List all users
$this->get('/users/create', 'UserController', 'create'); // Show create form
$this->post('/users/store', 'UserController', 'store');  // Process create form
$this->get('/users/{id}', 'UserController', 'show');     // Show specific user profile

$this->get('/profile', 'UserController', 'showUserProfile');
$this->get('/dashboard', 'UserController', 'showUserTripsDashboard');

// Home Route
$this->get('/', 'HomeController', 'index');
$this->get('/styleguide', 'HomeController', 'styleguide');

//Auth Routes
$this->get('/login', 'AuthController', 'login');
$this->post('/login/process', 'AuthController', 'processLogin');

$this->get('/register', 'AuthController', 'register');
$this->post('/register/process', 'AuthController', 'processRegister');

$this->get('/logout', 'AuthController', 'logout');