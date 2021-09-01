<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function() {
		echo view('404');
});
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

 $routes->get('get_sales', 'Cronjob::get_sales');

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('', ['filter'=>'isLoggedIn'],function($routes){
		$routes->get('pricing', 'Pricing::index');
		$routes->get('faturamento', 'Faturamento::index');
		$routes->get('faturamento/getGrossBillingDepto', 'Faturamento::getGrossBillingDepto');
		$routes->get('faturamento/getGrossBillingCategory', 'Faturamento::getGrossBillingCategory');
		$routes->get('faturamento/getAccumulatedMarginGrossBilling', 'Faturamento::getAccumulatedMarginGrossBilling');
		$routes->get('logsp', 'LogsPrecificacao::index');
		$routes->get('logspsac', 'LogsPrecificacaoSAC::index');
		$routes->get('logsprecificacao/search', 'LogsPrecificacao::search');
		$routes->post('logsprecificacao/response', 'LogsPrecificacao::getResponseJSON');
		$routes->get('simulator', 'Simulador::index');
		$routes->get('mgm', 'MGM::index');
		$routes->get('pbm', 'PBM::index');
		$routes->get('cronjob/mgm', 'Cronjob::mgm');
		$routes->get('cronjob/pbm', 'Cronjob::pbm');
		$routes->get('mgm/populateTable', 'MGM::populateTable');
		$routes->get('pbm/populateTable', 'PBM::populateTable');
		$routes->get('precificacao', 'Precificacao::index');
		$routes->get('pbm/analysis', 'PBM::analysis');
		$routes->get('pbm/perfomancePBM', 'PBM::perfomancePBM');
		$routes->get('pbm/getDataVanOrProgram', 'PBM::getDataVanOrProgram');
		$routes->get('pbm/sharePBM', 'PBM::sharePBM');
		$routes->get('falteiro', 'Falteiro::index');
		$routes->get('falteiro/getData', 'Falteiro::getData');
		$routes->get('tag', 'Tag::index');
		$routes->get('google', 'GoogleShopping::index');
});

$routes->group('', ['filter'=>'permissions'],function($routes){
		$routes->get('pricing', 'Pricing::index');
		$routes->get('faturamento', 'Faturamento::index');
		$routes->get('faturamento/getGrossBillingDepto', 'Faturamento::getGrossBillingDepto');
		$routes->get('faturamento/getGrossBillingCategory', 'Faturamento::getGrossBillingCategory');
		$routes->get('faturamento/getAccumulatedMarginGrossBilling', 'Faturamento::getAccumulatedMarginGrossBilling');
		$routes->get('logsp', 'LogsPrecificacao::index');
		$routes->get('logspsac', 'LogsPrecificacaoSAC::index');
		$routes->get('logsprecificacao/search', 'LogsPrecificacao::search');
		$routes->post('logsprecificacao/response', 'LogsPrecificacao::getResponseJSON');
		$routes->get('simulator', 'Simulador::index');
		$routes->get('mgm', 'MGM::index');
		$routes->get('pbm', 'PBM::index');
		$routes->get('cronjob/mgm', 'Cronjob::mgm');
		$routes->get('cronjob/pbm', 'Cronjob::pbm');
		$routes->get('mgm/populateTable', 'MGM::populateTable');
		$routes->get('pbm/populateTable', 'PBM::populateTable');
		$routes->get('precificacao', 'Precificacao::index');
		$routes->get('pbm/analysis', 'PBM::analysis');
		$routes->get('pbm/perfomancePBM', 'PBM::perfomancePBM');
		$routes->get('pbm/getDataVanOrProgram', 'PBM::getDataVanOrProgram');
		$routes->get('pbm/sharePBM', 'PBM::sharePBM');
		$routes->get('falteiro', 'Falteiro::index');
		$routes->get('falteiro/getData', 'Falteiro::getData');
		$routes->get('tag', 'Tag::index');
		$routes->get('google', 'GoogleShopping::index');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
