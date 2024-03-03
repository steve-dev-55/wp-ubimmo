<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  Ubimmo
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}

// Clear Database stored data
$biens_immobiliers = get_posts(array('post_type' => 'biens_immobiliers', 'numberposts' => -1));

foreach ($biens_immobiliers as $biens_immobilier) {
	wp_delete_post($biens_immobilier->ID, true);
}

// Access the database via SQL
global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'biens_immobiliers'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)");
