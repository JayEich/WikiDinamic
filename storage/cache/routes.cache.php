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
        'controller' => 'Admin\\AdminController',
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
      '#^admin/wikis/create$#' => 
      array (
        'uri' => 'admin/wikis/create',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'create',
        'name' => 'admin.wikis.create',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^admin/wikis$#' => 
      array (
        'uri' => 'admin/wikis',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'index',
        'name' => 'admin.wikis.index',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^admin/wikis/([^/]+)/edit$#' => 
      array (
        'uri' => 'admin/wikis/{uuid}/edit',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'edit',
        'name' => 'admin.wikis.edit',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
          0 => 'uuid',
        ),
      ),
      '#^user/dashboard$#' => 
      array (
        'uri' => 'user/dashboard',
        'controller' => 'UserController',
        'action' => 'index',
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
      '#^admin/wikis$#' => 
      array (
        'uri' => 'admin/wikis',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'store',
        'name' => 'admin.wikis.store',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
        ),
      ),
      '#^admin/wikis/([^/]+)$#' => 
      array (
        'uri' => 'admin/wikis/{uuid}',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'update',
        'name' => 'admin.wikis.update',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
          0 => 'uuid',
        ),
      ),
      '#^admin/wikis/([^/]+)/delete$#' => 
      array (
        'uri' => 'admin/wikis/{uuid}/delete',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'delete',
        'name' => 'admin.wikis.delete',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
          0 => 'uuid',
        ),
      ),
      '#^admin/wikis/([^/]+)/restore$#' => 
      array (
        'uri' => 'admin/wikis/{uuid}/restore',
        'controller' => 'App\\Controllers\\Admin\\WikiController',
        'action' => 'restore',
        'name' => 'admin.wikis.restore',
        'middleware' => 
        array (
          0 => 'auth',
          1 => 'admin',
        ),
        'params' => 
        array (
          0 => 'uuid',
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
    'admin.wikis.create' => 'admin/wikis/create',
    'admin.wikis.store' => 'admin/wikis',
    'admin.wikis.index' => 'admin/wikis',
    'admin.wikis.edit' => 'admin/wikis/{uuid}/edit',
    'admin.wikis.update' => 'admin/wikis/{uuid}',
    'admin.wikis.delete' => 'admin/wikis/{uuid}/delete',
    'admin.wikis.restore' => 'admin/wikis/{uuid}/restore',
    'user.dashboard' => 'user/dashboard',
    'home' => '',
  ),
  'timestamp' => 1746808075,
);