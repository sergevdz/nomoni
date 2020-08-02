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

$pends = new ControllerCollection(new ExpensesController(), '/spends');
$pends->get('/', 'getAll');
$pends->get('/{id:[0-9]+}', 'get');
$pends->post('/', 'create');
$pends->put('/{id:[0-9]+}', 'update');
$pends->delete('/{id:[0-9]+}', 'delete');
$pends->get('/daily', 'getDailyExpenses');
$pends->get('/monthly', 'getMonthlyExpenses');
$pends->get('/muchas', 'createMuchas');
$pends->post('/spends', 'getFilteredSpends');
$pends->get('/get-grouped-by-category', 'getSpendGroupedByCategory');
$pends->get('/get-grouped-by-type', 'getSpendGroupedByType');
$pends->get('/get-grouped-by-payment-method', 'getSpendGroupedByPaymentMethod');
$pends->get('/get-last-five-months', 'getLastFiveMonths');
$pends->get('/by-user/{userId:[0-9]+}', 'getByUser');
$app->mount($pends);
