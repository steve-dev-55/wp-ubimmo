<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Api;

use Ubi\Base\SettingsApi;
use Ubi\Base\BaseController;
use Ubi\Api\CronController;

class DashboardController extends BaseController
{
	public $settings;

	public $cronDisplay;

	public $pages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->cronDisplay = new CronController();

		$this->setPages();

		$this->settings->addPages($this->pages)->withSubPage('Tableau de bord')->register();
	}

	public function setPages()
	{
		$this->pages = array(
			array(
				'page_title' => 'Ubimmo',
				'menu_title' => 'Ubimmo',
				'capability' => 'manage_options',
				'menu_slug' => 'ubi_immo',
				'callback' => array($this, 'Dashboard'),
				'icon_url' => 'dashicons-store',
				'position' => 25
			)
		);
	}

	public function Dashboard()
	{
		$args = array(
			'orderby'       => 'id',
			'order'         => 'DESC',
			'number'        => '',
			'exclude_admin' => true,
			'show_fullname' => false,
			'feed'          => '',
			'feed_image'    => '',
			'feed_type'     => '',
			'echo'          => true,
			'style'         => 'list',
			'html'          => true,
			'exclude'       => '',
			'include'       => '',
			'role__in'      => array('author')
		);

		// Récupérer tous les annonceurs
		$liste_annonceurs = get_users($args);
		// Inclure la vue pour afficher la liste des options
		include("$this->plugin_path/templates/dashboard.php");
	}

	// Fonction pour vérifier si un utilisateur doit être affiché ou non
	public function do_check_annonceur($user)
	{
		// Obtenez la valeur à vérifier pour chaque utilisateur
		$value_to_check = get_the_author_meta('activation', $user->id);

		// Vérifiez si l'utilisateur doit être affiché ou non
		if ($value_to_check == '1') {
			return true;
		} else {
			return false;
		}
	}
}
