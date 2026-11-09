<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ============================================================================
// 1. OTENTIKASI (PUBLIC ROUTES)
// ============================================================================
$routes->get('login', 'Auth::login');
$routes->post('login/process', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// ============================================================================
// 2. ADMIN CONSOLE (PROTECTED ROUTES - WAJIB LOGIN VIA FILTER 'auth')
// ============================================================================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard Overview
    $routes->get('/', 'Dashboard::index');

    // Master Akun Polri (Kamsel & Polantas)
    $routes->group('police-accounts', function ($routes) {
        $routes->get('', 'PoliceAccounts::index');
        $routes->match(['get', 'post'], 'data', 'PoliceAccounts::datatable');
        $routes->get('create', 'PoliceAccounts::create');
        $routes->post('store', 'PoliceAccounts::store');
        $routes->get('edit/(:segment)', 'PoliceAccounts::edit/$1');
        $routes->post('update/(:segment)', 'PoliceAccounts::update/$1');
        $routes->get('delete/(:segment)', 'PoliceAccounts::delete/$1');
        $routes->get('toggle/(:segment)', 'PoliceAccounts::toggleStatus/$1');
    });

    // Manajemen Akun Administrator Console
    $routes->group('admin-users', function ($routes) {
        $routes->get('', 'AdminUsers::index');
        $routes->match(['get', 'post'], 'data', 'AdminUsers::datatable');
        $routes->get('create', 'AdminUsers::create');
        $routes->post('store', 'AdminUsers::store');
        $routes->get('edit/(:segment)', 'AdminUsers::edit/$1');
        $routes->post('update/(:segment)', 'AdminUsers::update/$1');
        $routes->get('delete/(:segment)', 'AdminUsers::delete/$1');
        $routes->get('toggle/(:segment)', 'AdminUsers::toggleStatus/$1');
    });

    // Master Sekolah Binaan & Radar Kamsel
    $routes->group('schools', function ($routes) {
        $routes->get('', 'Schools::index');
        $routes->match(['get', 'post'], 'data', 'Schools::datatable');
        $routes->get('create', 'Schools::create');
        $routes->post('store', 'Schools::store');
        $routes->get('edit/(:segment)', 'Schools::edit/$1');
        $routes->post('update/(:segment)', 'Schools::update/$1');
        $routes->get('delete/(:segment)', 'Schools::delete/$1');
        $routes->get('toggle/(:segment)', 'Schools::toggleStatus/$1');
    });

    // Master Akun Guru BK Sekolah
    $routes->group('bk-accounts', function ($routes) {
        $routes->get('', 'BkAccounts::index');
        $routes->match(['get', 'post'], 'data', 'BkAccounts::datatable');
        $routes->get('create', 'BkAccounts::create');
        $routes->post('store', 'BkAccounts::store');
        $routes->get('edit/(:segment)', 'BkAccounts::edit/$1');
        $routes->post('update/(:segment)', 'BkAccounts::update/$1');
        $routes->get('delete/(:segment)', 'BkAccounts::delete/$1');
        $routes->get('toggle/(:segment)', 'BkAccounts::toggleStatus/$1');
        $routes->get('toggle-primary/(:segment)', 'BkAccounts::togglePrimary/$1');
    });

    // Master Jenis Tilang & Pasal UU LLAJ
    $routes->group('violations', function ($routes) {
        $routes->get('', 'Violations::index');
        $routes->match(['get', 'post'], 'data', 'Violations::datatable');
        $routes->get('create', 'Violations::create');
        $routes->post('store', 'Violations::store');
        $routes->get('edit/(:segment)', 'Violations::edit/$1');
        $routes->post('update/(:segment)', 'Violations::update/$1');
        $routes->get('delete/(:segment)', 'Violations::delete/$1');
        $routes->get('toggle/(:segment)', 'Violations::toggleStatus/$1');
    });

    // Audit Log Kamsel & Tilang Siswa
    $routes->group('audit-logs', function ($routes) {
        $routes->get('inspections', 'AuditLogs::inspections');
        $routes->match(['get', 'post'], 'inspections/data', 'AuditLogs::datatableInspections');
        $routes->get('violations', 'AuditLogs::violations');
        $routes->match(['get', 'post'], 'violations/data', 'AuditLogs::datatableViolations');
    });

});
