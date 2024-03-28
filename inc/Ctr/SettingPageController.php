<?php

/**
 * @package  Ubimmo
 */

namespace Inc\Ctr;

use Inc\Base\SettingsApi;
use Inc\Base\BaseController;

/**
 * Code pour gérer le réglage
 */
class SettingPageController extends BaseController
{
	public $settings;

	public $callbacks;

	public $subpages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->setSubpages();

		add_action('admin_init',  array($this, 'init_general_settings'));

		$this->settings->addSubPages($this->subpages)->register();
	}

	public function setSubpages()
	{
		$this->subpages = array(
			array(
				'parent_slug' => 'ubi_immo',
				'page_title' => 'Ubimmo Réglages',
				'menu_title' => 'Réglages',
				'capability' => 'manage_options',
				'menu_slug' => 'ubimmo_reglage',
				'callback' => array($this, 'adminSetting'),
				'position' => 6

			)
		);
	}

	// Fonction pour afficher la page de réglages
	public function adminSetting()
	{
		// Vérifier les autorisations
		if (!current_user_can('manage_options')) {
			return;
		}

		// Inclure la vue pour afficher la liste des options
		include("$this->plugin_path/templates/settingsPage.php");
	}

	// Fonction pour initialiser les options de réglages
	public function init_general_settings()
	{
		add_settings_section('ubimmo_setting_section', 'Options d\'importations', array($this, 'ubimmo_options_section'), 'ubimmo_reglage');

		$options_select = array(
			'nbr_interval' => array('Nombre d\'annonces importées par Intervalle:', array('5', '10', '15')),
			'max_image_import' => array('Nombre d\'images max importées:', array('3', '5', '10')),
			'status_import' => array('Status des annonces:', array('publish', 'pending', 'draft'))
		);

		foreach ($options_select as $option_name => $option_label) {
			$options = array();
			$options = $option_label[1];
			add_settings_field(
				$option_name,
				$option_label[0],
				array($this, 'selectField'),
				'ubimmo_reglage',
				'ubimmo_setting_section',
				array(
					'name' => $option_name,
					'options' => $options
				)
			);
			register_setting('ubimmo_settings', $option_name);
		}

		$option_check = array(
			'nbr_pieces_import' => 'Nombre de pièces:',
			'nbr_chambre_import' => 'Nombre de chambres:',
			'surface' => 'Surface',
			'surface_habitable' => 'Surface habitable:',
			'garage' => 'Garage:',
			'terrasse' => 'Terrasse:',
			'dpe_etiquette_ges' => 'diagnostic d\'émission de gaz:',
			'dpe_etiquette_conso' => 'Diagnostic de consommation énergétique:'
		);

		foreach ($option_check as $option_name => $option_label) {
			add_settings_field(
				$option_name,
				$option_label,
				array($this, 'checkField'),
				'ubimmo_reglage',
				'ubimmo_setting_section',
				array('name' => $option_name)
			);
			register_setting('ubimmo_settings', $option_name);
		}
	}

	public function ubimmo_options_section()
	{
		echo '<p>Réglage des caractéristiques que vous souhaitez importer</p>';
	}


	// Afficher l'option de sélection
	public function selectField($args)
	{
		$name = $args['name'];
		$options = $args['options'];
		$selected_option = get_option($name);
		echo '<select name="' . $name . '">';

		foreach ($options as $value => $label) {
			$selected = selected($selected_option, $label, false);
			var_dump($selected);
			echo '<option value="' . esc_attr($label) . '" ' . $selected . '>' . esc_html($label) . '</option>';
		}

		echo '</select>';
	}

	//afficher l'option de chekbox
	public function checkField($args)
	{
		$name = $args['name'];
		// Afficher l'option de case à cocher
		$checked = get_option($name) ? 'checked' : '';

		echo '<div class="ui-toggle">
		<input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="true" ' . $checked . '><label for="' . esc_attr($name) . '">
		<div></div>
		</label></div></td>';
	}
}
