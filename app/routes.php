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
$this->get('/profile/update', 'UserController', 'updateUserProfile');
$this->get('/my-trips', 'UserController', 'showUserTripsDashboard');

// ALLERGY CONTROLLER ROUTES
$this->get('/allergy/add', 'AllergyController', 'addAllergy');
$this->get('/allergy/update', 'AllergyController', 'updateAllergy');
$this->get('/allergy/remove', 'AllergyController', 'removeAllergy');

// EMERGENCY CONTROLLER ROUTES
$this->get('/emergency-contact/add', 'EmergencyController', 'addEmergencyContact');
$this->get('/emergency-contact/update', 'EmergencyController', 'updateEmergencyContact');
$this->get('/emergency-contact/remove', 'EmergencyController', 'removeEmergencyContact');

// Home Route
$this->get('/', 'HomeController', 'index');
$this->get('/styleguide', 'HomeController', 'styleguide');

//Auth Routes
$this->get('/login', 'AuthController', 'login');
$this->post('/login/process', 'AuthController', 'processLogin');

$this->get('/register', 'AuthController', 'register');
$this->post('/register/process', 'AuthController', 'processRegister');

$this->get('/logout', 'AuthController', 'logout');