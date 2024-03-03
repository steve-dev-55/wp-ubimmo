<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Api;

use Ubi\Base\SettingsApi;
use Ubi\Base\BaseController;

/**
 * Code pour gérer le custom post type
 */
class CptBiensController extends BaseController
{
    public $settings;

    public function register()
    {
        $this->settings = new SettingsApi();

        add_action('init', array($this, 'create_biens_immobiliers_post_type'));
        add_action('manage_biens_immobiliers_posts_custom_column', array($this,  'custom_biens_immobiliers_column_data'), 10, 2);
        add_action('manage_biens_immobiliers_posts_columns', array($this, 'custom_biens_immobiliers_columns'));
        add_filter('manage_edit-biens_immobiliers_sortable_columns', array($this, 'custom_biens_immobiliers_sortable'));
    }

    // Créer un custom post type pour les biens immobiliers
    public function create_biens_immobiliers_post_type()
    {
        $labels = array(
            'name' => 'Biens immobiliers',
            'singular_name' => 'Bien immobilier',
            'add_new' => 'Ajouter un bien',
            'add_new_item' => 'Ajouter un bien immobilier',
            'edit_item' => 'Modifier un bien immobilier',
            'new_item' => 'Nouveau bien immobilier',
            'update_item'  => __('mis a jour', 'Ubimmo'),
            'all_items'    => __('Toutes les annonces', 'Ubimmo'),
            'view_item' => 'Voir le bien immobilier',
            'search_items' => 'Rechercher un bien immobilier',
            'not_found' => 'Aucun bien immobilier trouvé',
            'not_found_in_trash' => 'Aucun bien immobilier trouvé dans la corbeille'
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'has_archive' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-admin-home',
            'supports' => array('title', 'editor', 'thumbnail'), // 'custom-fields'
            'taxonomies' => array('type_bien', 'localisation', 'categories'),
            'rewrite' => array('slug' => 'biens-immobiliers')
        );
        register_post_type('biens_immobiliers', $args);

        $args1 =  array(
            'labels' => array(
                'name' => __('Types de bien'),
                'singular_name' => __('Type de bien')
            ),
            'hierarchical' => false,
            'rewrite' => array('slug' => 'type-bien')
        );
        register_taxonomy('type_bien', array('biens_immobiliers'), $args1);

        $args2 = array(
            'labels' => array(
                'name' => __('Localisation'),
                'singular_name' => __('Localisation')
            ),
            'hierarchical' => true,
            'rewrite' => array('slug' => 'localisation')
        );
        register_taxonomy('localisation', array('biens_immobiliers'), $args2);

        $args3 = array(
            'labels' => array(
                'name' => __('Categories'),
                'singular_name' => __('Catégories')
            ),
            'hierarchical' => true,
            'rewrite' => array('slug' => 'category')
        );
        register_taxonomy('category', array('biens_immobiliers'), $args3);
    }

    // Fonction pour personnaliser les colonnes du tableau des biens immobiliers
    public function custom_biens_immobiliers_columns($columns)
    {
        // Définir les nouvelles colonnes et leur ordre
        $new_columns = array(
            'cb' => $columns['cb'],
            'image' => __('Image du bien', 'Ubimmo'),
            'title' => $columns['title'],
            'prix' => __('Prix', 'Ubimmo'),
            'ville' => __('ville', 'Ubimmo'),
            'agent' => __('Agent', 'Ubimmo'),
            'date' => $columns['date']
        );

        // Retourner les colonnes modifiées
        return $new_columns;
    }

    // Fonction pour afficher les données des nouvelles colonnes
    public function custom_biens_immobiliers_column_data($column, $post_id)
    {
        switch ($column) {
            case 'image':
                if (has_post_thumbnail()) {
                    echo get_the_post_thumbnail($post_id, array(100, 100));
                }
                break;
            case 'prix':
                $prix = get_post_meta($post_id, 'prix', true);
                echo esc_html($prix);
                break;
            case 'ville':
                $ville = get_post_meta($post_id, 'ville', true);
                echo esc_html($ville);
                break;
            case 'agent':
                $agent = get_the_author_meta('display_name');
                echo esc_html($agent);
                break;
        }
    }


    public function custom_biens_immobiliers_sortable($columns)
    {
        $columns['agent'] = 'agent';
        $columns['prix'] = 'prix';

        return $columns;
    }
}
