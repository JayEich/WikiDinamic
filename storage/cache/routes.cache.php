<?php return array (
  'routes' => 
  array (
    'GET' => 
    array (
      '#^login$#' => 
      array (
        'uri' => 'login',
        'controller' => 'AuthController',
        'action' => 'login',
        'name' => 'login',
        'middleware' => 
        array (
          0 => 'guest',
        ),
        'params' => 
        array (
        ),
      ),
      '#^superadmin/dashboard$#' => 
      array (
        'uri' => 'superadmin/dashboard',
        'controller' => 'Superadmin\\DashboardController',
        'action' => 'index',
        'name' => 'superadmin.dashboard',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'superadmin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^superadmin/client/edit$#' => 
      array (
        'uri' => 'superadmin/client/edit',
        'controller' => 'Superadmin\\ClientController',
        'action' => 'edit',
        'name' => 'superadmin.client.edit',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'superadmin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^admin/dashboard$#' => 
      array (
        'uri' => 'admin/dashboard',
        'controller' => 'AdminController',
        'action' => 'dashboard',
        'name' => 'admin.dashboard',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^user/dashboard$#' => 
      array (
        'uri' => 'user/dashboard',
        'controller' => 'UserController',
        'action' => 'dashboard',
        'name' => 'user.dashboard',
        'middleware' => 
        array (
          0 => 'auth',
        ),
        'params' => 
        array (
        ),
      ),
      '#^$#' => 
      array (
        'uri' => '',
        'controller' => 'HomeController',
        'action' => 'index',
        'name' => 'home',
        'middleware' => 
        array (
        ),
        'params' => 
        array (
        ),
      ),
    ),
    'POST' => 
    array (
      '#^login$#' => 
      array (
        'uri' => 'login',
        'controller' => 'AuthController',
        'action' => 'login',
        'name' => NULL,
        'middleware' => 
        array (
          0 => 'guest',
        ),
        'params' => 
        array (
        ),
      ),
      '#^logout$#' => 
      array (
        'uri' => 'logout',
        'controller' => 'AuthController',
        'action' => 'logout',
        'name' => 'logout',
        'middleware' => 
        array (
          0 => 'auth',
        ),
        'params' => 
        array (
        ),
      ),
      '#^superadmin/client/update$#' => 
      array (
        'uri' => 'superadmin/client/update',
        'controller' => 'Superadmin\\ClientController',
        'action' => 'update',
        'name' => 'superadmin.client.update',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'superadmin',
        ),
        'params' => 
        array (
        ),
      ),
    ),
  ),
  'named' => 
  array (
    'login' => 'login',
    'logout' => 'logout',
    'superadmin.dashboard' => 'superadmin/dashboard',
    'superadmin.client.edit' => 'superadmin/client/edit',
    'superadmin.client.update' => 'superadmin/client/update',
    'admin.dashboard' => 'admin/dashboard',
    'user.dashboard' => 'user/dashboard',
    'home' => '',
  ),
  'timestamp' => 1746207129,
);