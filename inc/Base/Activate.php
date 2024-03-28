<?php
/**
 * @package  AlecadddPlugin
 */
namespace Inc\Base;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();

        		$default = array();

		if (!get_option('ubimmo_settings')) {

			update_option('ubimmo_settings', $default);
			update_option('nbr_interval', 5);
			update_option('max_image_import', 5);
			update_option('status_import', 'publish');
			update_option('nbr_pieces_import', true);
			update_option('nbr_chambre_import', true);
			update_option('surface', true);
			update_option('surface_habitable', true);
			update_option('garage', true);
			update_option('terrasse', true);
			update_option('dpe_etiquette_ges', true);
			update_option('dpe_etiquette_conso', true);
			update_option('pays', 'Fr');
			update_option('zoom_map', 300);
			update_option('h_zoom', 100);
		}
	}
}