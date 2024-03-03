<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Base;

class BaseController
{
	public $plugin_path;

	public $plugin_url;

	public $plugin;

	public $managers = array();

	public function __construct()
	{
		$this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
		$this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
		$this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/wp-ubi-immo.php';

		$this->managers = array(
			'cpt_manager' => 'Activate CPT Manager',
			'taxonomy_manager' => 'Activate Taxonomy Manager',
			'media_widget' => 'Activate Media Widget',
			'gallery_manager' => 'Activate Gallery Manager',
			'testimonial_manager' => 'Activate Testimonial Manager',
			'templates_manager' => 'Activate Custom Templates',
			'login_manager' => 'Activate Ajax Login/Signup',
			'membership_manager' => 'Activate Membership Manager',
			'chat_manager' => 'Activate Chat Manager'
		);
	}

	public function generate_text_input($label, $name, $value = null, $disc = null, $class = '')
	{
		// Générer le label avec l'attribut "for" correspondant à l'ID de l'input
		$label_html = '<tr><th scope="row">
		<label for="' . esc_attr($name) . '">' . esc_html($label) . ':</label>
		</th>
		';

		// Générer l'input de type text avec l'attribut "id" correspondant à l'ID généré
		$input_html = '<td>
		<input type="text" class="' . esc_attr($class) . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">
		<p>' . $disc . '</p>
		</td>
		</tr>';

		// Retourner le label et l'input générés
		return $label_html . $input_html;
	}

	public function generate_checkbox_input($label, $name, $value = null, $class = '')
	{
		// Vérifier si la valeur est différente de null et définir la propriété "checked" en conséquence
		$checked = ($value == true) ? 'checked' : '';

		// Générer l'input de type checkbox avec l'attribut "id" correspondant à l'ID généré et la propriété "checked"
		$input_html = '
		<tr class="ui-toggle">
		<th scope="row">
		<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>
		</th>
		<td>
		<div class="ui-toggle">
		<input type="checkbox" class="' . esc_attr($class) . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="true" ' . $checked . '><label for="' . esc_attr($name) . '">
		<div></div>
		</label></div></td></tr>';
		// Retourner le label et l'input générés
		return $input_html;
	}

	function generate_select_input($label, $name, $options, $value = null, $class = '')
	{
		// Générer le label avec l'attribut "for" correspondant à l'ID de l'input
		$label_html = '<tr><th scope="row">
		<label for="' . esc_attr($name) . '">' . esc_html($label) . ':</label>                            
		</th>';

		// Générer l'input de type select avec l'attribut "id" correspondant à l'ID généré
		$input_html = '<td><select id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" class="' . esc_attr($class) . '">';

		// Ajouter une option vide par défaut si la valeur est null
		if ($value === null) {
			$input_html .= '<option value=""></option>';
		}

		// Générer chaque option en bouclant sur le tableau d'options
		foreach ($options as $option_label) {
			// Déterminer si cette option est sélectionnée ou non
			$selected = ($option_label == $value) ? ' selected' : '';

			// Générer l'option avec le label et la valeur correspondants
			$input_html .= '<option value="' . esc_attr($option_label) . '"' . $selected . '>' . esc_html($option_label) . '</option>';
		}

		// Fermer la balise select
		$input_html .= '</select> </td></tr>';

		// Retourner le label et l'input générés
		return $label_html . $input_html;
	}

	public function limit_text($text, $limit)
	{
		if (strlen($text) > $limit) {
			$text = substr($text, 0, $limit) . '...';
		}
		return $text;
	}

	public function display_message($message, $type = 'success')
	{
		$class = ($type == 'success') ? 'notice notice-success is-dismissible' : 'notice notice-error is-dismissible';
		$html = '<div class="' . $class . '"><p>' . $message . '</p></div>';
		echo $html;
	}

	public function activated(string $key)
	{
		$option = get_option('ubimmo');

		return isset($option[$key]) ? $option[$key] : false;
	}
}
