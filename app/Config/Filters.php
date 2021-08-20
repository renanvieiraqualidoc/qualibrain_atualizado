<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		'isLoggedIn' => \App\Filters\LoginFilter::class,
		'hasPermission' => \App\Filters\PermissionsFilter::class
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
			'hasPermission' => ['except' => [ '/',
																				'qualiuser', 'qualiuser/forgot_password', 'qualiuser/register',
																				'auth/login', 'auth/denied', 'auth/logout',
																				'relatorio',
																				'profile/change_password', 'profile/change',
																				'pricing/productsGroups', 'pricing/tableInfo', 'pricing/competitorInfo', 'pricing/blistersInfo', 'pricing/margin', 'pricing/getSalesProducts', 'pricing/getSalesRMS',
																				'logsprecificacao/search', 'logsprecificacao/response',
																				'simulador/simulate', 'simulador/tableMarginSimulator',
																				'mgm/populateTable',
																				'get_sales',
																				'precificacao/updateSkus',
																				'tag/updateSkus',
																				'faturamento/getGrossBillingDepto', 'faturamento/getGrossBillingCategory', 'faturamento/getAccumulatedMarginGrossBilling',
																				'pbm/populateTable', 'pbm/analysis', 'pbm/perfomancePBM', 'pbm/getDataVanOrProgram', 'pbm/sharePBM',
																				'falteiro/getData' ]]
			// 'honeypot',
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [];
}
