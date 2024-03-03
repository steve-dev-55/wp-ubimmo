<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Api;

use Ubi\Base\SettingsApi;
use Ubi\Base\BaseController;

/**
 * Code pour gérer les metas de l'annonce
 */
class MetaBoxesController extends BaseController
{
    public $settings;

    public function register()
    {
        $this->settings = new SettingsApi();

        add_action('add_meta_boxes', array($this, 'add_biens_immobiliers_meta_box'));
        add_action('save_post', array($this, 'save_biens_immobiliers_meta_data'));
    }

    // Fonction pour afficher le formulaire d'édition des métadonnées du bien immobilier
    public function biens_immobiliers_meta_box_type($post)
    {
        wp_nonce_field('biens_immobiliers_meta_box', 'biens_immobiliers_meta_box_nonce');

        include("$this->plugin_path/templates/boxes/bien_immo_box_type.php");
    }

    // Fonction pour afficher le formulaire d'édition des métadonnées du bien immobilier
    public function biens_immobiliers_meta_box_caracteristique($post)
    {
        include("$this->plugin_path/templates/boxes/biens_immo_box_caracteristique.php");
    }

    // Fonction pour afficher le formulaire d'édition des métadonnées du bien immobilier
    public function biens_immobiliers_meta_box_localisation($post)
    {
        include("$this->plugin_path/templates/boxes/biens_immo_box_localisation.php");
    }

    // Fonction pour afficher le formulaire d'édition des métadonnées du bien immobilier
    public function biens_immobiliers_meta_box_energetique($post)
    {
        include("$this->plugin_path/templates/boxes/biens_immo_box_energie.php");
    }

    // Fonction pour enregistrer les métadonnées du bien immobilier
    public function save_biens_immobiliers_meta_data($post_id)
    {
        // Vérifier si le nonce est présent et valide
        if (!isset($_POST['biens_immobiliers_meta_box_nonce']) || !wp_verify_nonce($_POST['biens_immobiliers_meta_box_nonce'], 'biens_immobiliers_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Vérifier si l'utilisateur a la permission de modifier le post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Définir les noms des champs de métadonnées
        $meta_fields = array(
            'mandat_type',
            'reference',
            'type_prestaton',
            'nb_pieces',
            'surface',
            'prix',
            'garage',
            'terrasse',
            'ville',
            'code_postal',
            'dpe_etiquette_ges',
            'dpe_etiquette_conso'
        );

        // Créer un tableau pour stocker les valeurs des métadonnées
        $meta_values = array();

        // Parcourir les champs de métadonnées et les enregistrer dans le tableau
        foreach ($meta_fields as $field) {
            $meta_values[$field] = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
        }

        // Enregistrer les valeurs des métadonnées dans la base de données
        foreach ($meta_values as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }

    // Ajouter une meta box pour les métadonnées du bien immobilier
    public function add_biens_immobiliers_meta_box()
    {
        add_meta_box(
            'biens_immobiliers_meta_box_type',
            'Type de bien immobilier',
            array($this, 'biens_immobiliers_meta_box_type'),
            'biens_immobiliers',
            'normal',
            'high'
        );

        add_meta_box(
            'biens_immobiliers_meta_box_caracteristique',
            'Caractéristiques du bien immobilier',
            array($this, 'biens_immobiliers_meta_box_caracteristique'),
            'biens_immobiliers',
            'normal',
            'high'
        );

        add_meta_box(
            'biens_immobiliers_meta_box_localisation',
            'Localisation du bien immobilier',
            array($this, 'biens_immobiliers_meta_box_localisation'),
            'biens_immobiliers',
            'normal',
            'high'
        );

        add_meta_box(
            'biens_immobiliers_meta_box_energetique',
            'Test energetique du bien immobilier',
            array($this, 'biens_immobiliers_meta_box_energetique'),
            'biens_immobiliers',
            'normal',
            'high'
        );
    }
}
