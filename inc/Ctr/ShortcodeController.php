<?php

/**
 * @package  Ubimmo
 */

namespace Inc\Ctr;

use WP_Query;

/**
 * code pour gérer les shortcodes
 */
class ShortcodeController
{

    public function register()
    {
        // Enregistrer le shortcode dans WordPress
        add_shortcode('annonces_immobilieres', array($this, 'shortcode_annonces_immobilieres'));
    }


    // Fonction pour le shortcode annonces_immobilieres
    function shortcode_annonces_immobilieres($atts)
    {
        // Récupérer les attributs du shortcode
        $atts = shortcode_atts(
            array(
                'type' => '',
                'ville' => '',
                'categorie' => '',
            ),
            $atts,
            'annonces_immobilieres'
        );

        // Construction de la requête pour récupérer les annonces en fonction des attributs
        $args = array(
            'post_type' => 'biens_immobiliers', // Type de publication personnalisé pour les annonces
            'posts_per_page' => 12, // Afficher toutes les annonces
        );

        // Ajouter des conditions pour les attributs type et ville s'ils sont définis
        if (!empty($atts['type'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'type_bien',
                'field'    => 'name', // utiliser 'id', 'slug' ou 'name'
                'terms'    => $atts['type'], //  terme de la taxonomie
            );
        }

        if (!empty($atts['ville'])) {
            $args['meta_query'][] = array(
                'key' => 'ville', // Clé personnalisée pour la ville
                'value' => $atts['ville'],
                'compare' => '='
            );
        }

        if (!empty($atts['categorie'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'category',
                'field'    => 'name', // utiliser 'id', 'slug' ou 'name'
                'terms'    => $atts['categorie'], //  terme de la taxonomie
            );
        }
        // Exécution de la requête
        $query = new WP_Query($args);

        // Construction du contenu à afficher
        $content = '<div class="annonces-immobilieres">';
        $content .= '<h2>Annonces Immobilières</h2>';

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $image_url = get_the_post_thumbnail_url(get_the_ID(), '');
                $content .= '<div class="card">';
                $content .= '<img src="' . $image_url . '" class="card-img-top" alt="Image de l\'annonce">';
                $content .= '<div class="card-body">';
                $content .= '<h5 class="card-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h5>';
                $content .= '<p class="card-text">' . get_the_excerpt() . '</p>';
                $content .= '</div>';
                $content .= '</div>';
            }
            wp_reset_postdata();
        } else {
            $content .= '<p>Aucune annonce trouvée pour les critères sélectionnés.</p>';
        }

        $content .= '</div>';

        return $content;
    }
}
