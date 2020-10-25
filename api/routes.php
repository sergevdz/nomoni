<?php

/**
 * Add your routes here
 */

$auth = new ControllerCollection(new AuthController(), '/auth');
$auth->post('/login', 'login');
$auth->post('/signup', 'signup');
$app->mount($auth);

$users = new ControllerCollection(new UsersController(), '/users');
$users->get('/profile', 'getProfile');
$app->mount($users);

$types = new ControllerCollection(new TypesController(), '/types');
$types->get('/', 'getTypes');
$types->get('/{id:[0-9]+}', 'getType');
$types->get('/options', 'getOptions');
$types->post('/', 'create');
$types->put('/{id:[0-9]+}', 'update');
$types->delete('/{id:[0-9]+}', 'delete');
$app->mount($types);

$paymentMethods = new ControllerCollection(new PaymentMethodsController(), '/payment-methods');
$paymentMethods->get('/', 'getPaymentMethods');
$paymentMethods->get('/{id:[0-9]+}', 'getPaymentMethod');
$paymentMethods->get('/options', 'getOptions');
$paymentMethods->post('/', 'create');
$paymentMethods->put('/{id:[0-9]+}', 'update');
$paymentMethods->delete('/{id:[0-9]+}', 'delete');
$app->mount($paymentMethods);

$categories = new ControllerCollection(new CategoriesController(), '/categories');
$categories->get('/', 'getCategories');
$categories->get('/{id:[0-9]+}', 'getCategory');
$categories->get('/options', 'getOptions');
$categories->post('/', 'create');
$categories->put('/{id:[0-9]+}', 'update');
$categories->delete('/{id:[0-9]+}', 'delete');
$app->mount($categories);

$expenses = new ControllerCollection(new ExpensesController(), '/expenses');
$expenses->get('/', 'getAll');
$expenses->get('/{id:[0-9]+}', 'get');
$expenses->post('/', 'create');
$expenses->put('/{id:[0-9]+}', 'update');
$expenses->delete('/{id:[0-9]+}', 'delete');
$expenses->get('/daily-amount', 'getDailyAmount');
// $expenses->get('/monthly', 'getMonthlyExpenses');
$expenses->get('/monthly-amount', 'getMonthlyAmount');
$expenses->post('/expenses', 'getFilteredSpends');
$expenses->get('/get-grouped-by-category', 'getSpendGroupedByCategory');
$expenses->get('/get-grouped-by-type', 'getSpendGroupedByType');
$expenses->get('/get-grouped-by-payment-method', 'getSpendGroupedByPaymentMethod');
$expenses->get('/get-last-five-months', 'getLastFiveMonths');
$expenses->get('/by-user/{userId:[0-9]+}', 'getByUser');
$app->mount($expenses);
