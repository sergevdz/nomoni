<?php

/**
 * Add your routes here
 */

$auth = new ControllerCollection(new AuthController(), '/auth');
$auth->post('/login', 'login');
$auth->post('/signup', 'signup');
$app->mount($auth);

$users = new ControllerCollection(new UsersController(), '/users');
$users->get('/profile', 'profile');
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

$spends = new ControllerCollection(new SpendsController(), '/spends');
$spends->get('/', 'getSpends');
$spends->get('/{id:[0-9]+}', 'getSpend');
$spends->post('/', 'create');
$spends->put('/{id:[0-9]+}', 'update');
$spends->delete('/{id:[0-9]+}', 'delete');
$spends->get('/daily', 'getDailyExpenses');
$spends->get('/monthly', 'getMonthlyExpenses');
$spends->get('/muchas', 'createMuchas');
$spends->post('/spends', 'getFilteredSpends');
$spends->get('/get-grouped-by-category', 'getSpendGroupedByCategory');
$spends->get('/get-grouped-by-type', 'getSpendGroupedByType');
$spends->get('/get-grouped-by-payment-method', 'getSpendGroupedByPaymentMethod');
$spends->get('/get-last-five-months', 'getLastFiveMonths');
$app->mount($spends);

$router->addGet('/users/{id:[0-9]+}/spends', 'SpendsController::getByUser');