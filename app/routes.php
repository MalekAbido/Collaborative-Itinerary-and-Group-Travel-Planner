<?php
/**
 * ═══════════════════════════════════════════════════
 * Application Routes
 * ═══════════════════════════════════════════════════
 */

// User Module Routes
// $this->get('/users', 'UserController', 'index');         // List all users
// $this->get('/users/create', 'UserController', 'create'); // Show create form
// $this->post('/users/store', 'UserController', 'store');  // Process create form
// $this->get('/users/{id}', 'UserController', 'show');     // Show specific user profile

$this->get('/profile', 'UserController', 'showUserProfile');
$this->post('/profile/update', 'UserController', 'updateUserProfile');
$this->get('/dashboard', 'UserController', 'showUserTripsDashboard');

// ALLERGY CONTROLLER ROUTES
$this->post('/allergy/add', 'AllergyController', 'addAllergy');
$this->post('/allergy/update', 'AllergyController', 'updateAllergy');
$this->post('/allergy/remove', 'AllergyController', 'removeAllergy');

// EMERGENCY CONTROLLER ROUTES
$this->post('/emergency-contact/add', 'EmergencyController', 'addEmergencyContact');
$this->post('/emergency-contact/update', 'EmergencyController', 'updateEmergencyContact');
$this->post('/emergency-contact/remove', 'EmergencyController', 'removeEmergencyContact');

// Home Route
$this->get('/', 'HomeController', 'index');
$this->get('/styleguide', 'HomeController', 'styleguide');

//Auth Routes
$this->get('/login', 'AuthController', 'login');
$this->post('/login/process', 'AuthController', 'processLogin');

$this->get('/register', 'AuthController', 'register');
$this->post('/register/process', 'AuthController', 'processRegister');

$this->get('/logout', 'AuthController', 'logout');

//Finance Overview Dashboard
$this->get('/finance/dashboard/{id}', 'FinanceController', 'dashboard');
// Expense routes
$this->get('/finance/expense/add', 'ExpenseController', 'showAddForm');         // add expense form
$this->post('/finance/expense/create', 'ExpenseController', 'createExpense');   // process the form 
$this->get('/finance/expense/details', 'ExpenseController', 'getExpenseDetails'); // breakdown the given expense

