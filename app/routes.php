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

// Home Route
$this->get('/', 'HomeController', 'index');
$this->get('/styleguide', 'HomeController', 'styleguide');
