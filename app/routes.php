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
$this->post('/finance/update-settings/{id}', 'FinanceController', 'updateSettings');
$this->post('/finance/create-fund/{id}', 'FinanceController', 'createGroupFund');

// Expense routes
$this->get('/finance/expense/add/{id}', 'ExpenseController', 'showAddForm');      // add expense form
$this->post('/finance/expense/create', 'ExpenseController', 'createExpense');     // process the form
$this->get('/finance/expense/details', 'ExpenseController', 'getExpenseDetails'); // breakdown the given expense
$this->post('/finance/expense/delete', 'ExpenseController', 'deleteExpense');     // delete expense

// Creating a trip
$this->get('/itinerary/create', 'ItineraryController', 'create');
$this->post('/itinerary/store', 'ItineraryController', 'store');

// Manage Activities
$this->get('/itinerary/{id}/activity/create', 'ActivityController', 'create');
$this->post('/itinerary/{id}/activity/store', 'ActivityController', 'store');
$this->get('/itinerary/{id}/activity/{id}', 'ActivityController', 'show');
$this->post('/itinerary/{id}/activity/{id}/updateAttendance', 'ActivityController', 'updateAttendance');
$this->post('/itinerary/{id}/activity/{id}/delete', 'ActivityController', 'delete');

// Manage Proposals
$this->get('/itinerary/{id}/proposals', 'ProposalController', 'index');
$this->post('/itinerary/{id}/proposals/{activityId}/approve', 'ProposalController', 'approve');
$this->post('/itinerary/{id}/proposals/{activityId}/reject', 'ProposalController', 'reject');

// Managing trip settings
$this->get('/itinerary/settings/{id}', 'ItineraryController', 'settings');
$this->post('/itinerary/update/{id}', 'ItineraryController', 'update');
$this->post('/itinerary/destroy/{id}', 'ItineraryController', 'destroy');

// Dashboard & Members (From your UML diagram, for later!)
$this->get('/itinerary/dashboard/{id}', 'ItineraryController', 'getDashboard');
$this->get('/itinerary/members/{id}', 'ItineraryController', 'getMembersList');

// Poll Routes
$this->get('/itinerary/polls/{id}', 'PollController', 'index');
$this->post('/poll/vote', 'PollController', 'vote');
$this->post('/poll/store', 'PollController', 'store');
$this->post('/poll/close-early', 'PollController', 'closeEarly');
$this->post('/poll/reopen', 'PollController', 'reopen');

// 1. View the Members Dashboard
$this->get('/itinerary/members/{id}', 'TripMemberController', 'index');

// 2. Invite a new member
$this->post('/itinerary/members/invite/{id}', 'TripMemberController', 'store');

// 3. Update a member's role
$this->post('/itinerary/members/updateRole/{id}', 'TripMemberController', 'updateRole');

// 4. Remove a member
$this->post('/itinerary/members/remove/{id}', 'TripMemberController', 'destroy');

// Group Fund (Kitty)
$this->post('/fund/contribute/{id}', 'CommonPoolController', 'contribute');
$this->post('/finance/expense/refund', 'ExpenseController', 'refundExpense');

// History & Audit Log
$this->get('/itinerary/{id}/history', 'HistoryController', 'index');
$this->post('/itinerary/{id}/history/revert/{entryId}', 'HistoryController', 'revert');
