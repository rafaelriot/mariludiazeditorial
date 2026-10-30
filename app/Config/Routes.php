<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---------------------------------------------------------------------
// Sitio público (lectura libre, sin login)
// ---------------------------------------------------------------------
$routes->get('/', 'Site\Feed::index', ['as' => 'home']);
$routes->get('categoria/(:segment)', 'Site\Feed::category/$1', ['as' => 'category']);
$routes->get('historia/(:segment)', 'Site\Posts::show/$1', ['as' => 'post']);
$routes->post('historia/(:num)/like', 'Site\Likes::toggle/$1', ['as' => 'post.like']);
$routes->post('historia/(:num)/comentar', 'Site\Posts::comment/$1', ['as' => 'post.comment']);
$routes->get('sobre-mi', 'Site\Pages::about', ['as' => 'about']);
$routes->get('sitemap.xml', 'Site\Seo::sitemap', ['as' => 'sitemap']);
$routes->get('feed.xml', 'Site\Seo::rss', ['as' => 'rss']);
$routes->get('robots.txt', 'Site\Seo::robots');

// Login con Google / Gmail para lectores
$routes->get('auth/google', 'Site\Auth::google', ['as' => 'google.login']);
$routes->get('auth/google/callback', 'Site\Auth::googleCallback', ['as' => 'google.callback']);
$routes->get('auth/google/mock', 'Site\Auth::googleMock', ['as' => 'google.mock']);
$routes->get('auth/logout', 'Site\Auth::logout', ['as' => 'reader.logout']);

// Biblioteca de componentes (solo desarrollo)
if (ENVIRONMENT === 'development') {
    $routes->get('styleguide', 'Site\Styleguide::index', ['as' => 'styleguide']);
}

// ---------------------------------------------------------------------
// Autenticación (Shield) — sin registro público
// ---------------------------------------------------------------------
service('auth')->routes($routes, ['except' => ['register', 'magic-link']]);

// ---------------------------------------------------------------------
// Panel administrativo
// ---------------------------------------------------------------------
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'group:admin,editor'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Dashboard::index', ['as' => 'admin.dashboard']);

    // Historias
    $routes->get('historias', 'Posts::index', ['as' => 'admin.posts']);
    $routes->get('historias/nueva', 'Posts::new', ['as' => 'admin.posts.new']);
    $routes->post('historias', 'Posts::create', ['as' => 'admin.posts.create']);
    $routes->get('historias/(:num)/editar', 'Posts::edit/$1', ['as' => 'admin.posts.edit']);
    $routes->post('historias/(:num)', 'Posts::update/$1', ['as' => 'admin.posts.update']);
    $routes->post('historias/(:num)/eliminar', 'Posts::delete/$1', ['as' => 'admin.posts.delete']);

    // Multimedia (galería y CKEditor)
    $routes->post('media', 'Media::upload', ['as' => 'admin.media.upload']);
    $routes->post('media/editor', 'Media::editorUpload', ['as' => 'admin.media.editor']);
    $routes->post('media/(:num)', 'Media::update/$1', ['as' => 'admin.media.update']);
    $routes->post('media/(:num)/eliminar', 'Media::delete/$1', ['as' => 'admin.media.delete']);

    // Categorías
    $routes->get('categorias', 'Categories::index', ['as' => 'admin.categories']);
    $routes->post('categorias', 'Categories::create', ['as' => 'admin.categories.create']);
    $routes->post('categorias/(:num)', 'Categories::update/$1', ['as' => 'admin.categories.update']);
    $routes->post('categorias/(:num)/eliminar', 'Categories::delete/$1', ['as' => 'admin.categories.delete']);

    // Solo administradores
    $routes->group('', ['filter' => 'group:admin'], static function (RouteCollection $routes): void {
        $routes->get('usuarios', 'Users::index', ['as' => 'admin.users']);
        $routes->get('usuarios/nuevo', 'Users::new', ['as' => 'admin.users.new']);
        $routes->post('usuarios', 'Users::create', ['as' => 'admin.users.create']);
        $routes->get('usuarios/(:num)/editar', 'Users::edit/$1', ['as' => 'admin.users.edit']);
        $routes->post('usuarios/(:num)', 'Users::update/$1', ['as' => 'admin.users.update']);
        $routes->post('usuarios/(:num)/eliminar', 'Users::delete/$1', ['as' => 'admin.users.delete']);

        $routes->get('ajustes', 'Settings::index', ['as' => 'admin.settings']);
        $routes->post('ajustes', 'Settings::update', ['as' => 'admin.settings.update']);
    });
});
