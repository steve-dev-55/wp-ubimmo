<?php

/**
 * @package  Ubimmo
 */

namespace Inc\Ctr;

use Inc\Base\SettingsApi;
use Inc\Base\BaseController;

/**
 * Code pour gérer les imports 
 */
class ImportController extends BaseController
{
    public $settings;

    public function register()
    {
        $this->settings = new SettingsApi();
		add_action('ubimmo_cron_job', array($this, 'ubimmo_do_cron_job'));
    }

	public function import_xml($xml)
	{
		// Récupérer le contenu du fichier XML
		$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
		$xml_content = file_get_contents($xml, false, $context);
		// Charger le contenu XML dans un objet SimpleXML
		$xml_data = simplexml_load_string($xml_content);

		if ($xml_data === false) {
			wp_die('Erreur lors du chargement du XML. Veuillez vérifier le lien.');
		}

		return $xml_data;
	}

    public function update_user($xml_data, $id_user)
    {
        //import data
        $data = $this->import_xml($xml_data);

        if (!empty($data)) {
            //serialistion et test des données utilisateurs
            $email_user = htmlspecialchars($data->coordonnees->email);
            $display_name = isset($data->coordonnees->nom) ? htmlspecialchars($data->coordonnees->nom) : '';
            $client_telephone = isset($data->coordonnees->telephone) ? htmlspecialchars($data->coordonnees->telephone) : '';
            $client_adresse_ville = isset($data->coordonnees->adresse->ville) ? htmlspecialchars($data->coordonnees->adresse->ville) : '';
			
            //mettre a jour les données de l'utilisateur
            if (empty(get_user_by('email', $email_user))) {
                $test = wp_update_user(
                    array(
                        'ID'             => $id_user,
                        'user_email'     => $email_user,
                        'last_name'      => $display_name,
                        'meta_input' => array(
                            'phone'  => $client_telephone,
                            'city'   => $client_adresse_ville
                        )
                    )
                );
            }
        }
    }

    public function create_post($xml_data, $id_user)
    {
        $post_type = 'biens_immobiliers';
        //import data
        $datas = $this->import_xml($xml_data);
        if (!empty($datas)) {

            // Limiter le nombre de boucles à max_loops
            $max_loops = get_option('nbr_interval');

            $loop_count = 0;

            foreach ($datas->annonce as $annonces) {

                $import_id = (int)$annonces['id'];
                // annuler l'importation des duplicate annonces
                $existing_posts = get_posts(array(
                    'post_type' => $post_type,
                    'meta_key' => 'import_id',
                    'meta_value' => $import_id
                ));
                if (empty($existing_posts)) {
                    //créer la catégorie a partir du type de prestation
                    $cat_type = isset($annonces->prestation->type) ? htmlspecialchars($annonces->prestation->type) : '';
                    $category_id = $this->get_category_id($cat_type);

                    if (empty($annonces->prestation->prix)) {
                        $prix = (float) $annonces->prestation->loyer;
                    } else {
                        $prix = (float) $annonces->prestation->prix;
                    }
                    $code_postal = isset($annonces->bien->code_postal) ? (float) $annonces->bien->code_postal : '';
                    $data_location = $this->get_reg_and_dep_taxonomies_id($code_postal);

                    // Créer un tableau de données pour la nouvelle annonce
                    $post_data = array(
                        'post_author' => $id_user,
                        'post_title' => (string)$annonces->titre,
                        'post_content' => htmlspecialchars($annonces->texte),
                        'post_status' => get_option('status_import'),
                        'post_type' => $post_type,
                        'import_id' => $import_id,
                        'post_category' => array($category_id),
                        'tax_input' => array(
                            'localisation'   => array($data_location['region_id'], $data_location['department_id']),
                            'type_bien' => isset($annonces->bien->libelle_type) ? htmlspecialchars($annonces->bien->libelle_type) : '',
                        ),
                        'meta_input' => array(
                            'prix' => $prix,
                            'mandat_type' => isset($annonces->prestation->mandat_type) ? htmlspecialchars($annonces->prestation->mandat_type) : '',
                            'surface' => isset($annonces->bien->surface) and get_option('surface') !== false ? (float) $annonces->bien->surface : '',
                            'surface_habitable' => isset($annonces->bien->surface_habitable) and get_option('surface_habitable') !== false ? (float) $annonces->bien->surface_habitable : '',
                            'nb_pieces' => isset($annonces->bien->nb_pieces) and get_option('nbr_pieces_import') !== false ? (int) $annonces->bien->nb_pieces : '',
                            'nb_chambres' => isset($annonces->bien->nb_chambres) and get_option('nbr_chambre_import') !== false ? (int) $annonces->bien->nb_chambres : '',
                            'garage' => isset($annonces->bien->garage) and get_option('garage') !== false ? (bool)($annonces->bien->garage) : '',
                            'terrasse' => isset($annonces->bien->terrasse) and get_option('terrasse') !== false ? (bool)($annonces->bien->terrasse) : '',
                            'nb_stationnements' => isset($annonces->bien->nb_stationnements) ? (int) $annonces->bien->nb_stationnements : '',
                            'ville' => isset($annonces->bien->ville) ? htmlspecialchars($annonces->bien->ville) : '',
                            'code_postal' => $code_postal,
                            'dpe_etiquette_ges' => isset($annonces->bien->diagnostiques->dpe_etiquette_ges) and get_option('dpe_etiquette_ges') !== false ? htmlspecialchars($annonces->bien->diagnostiques->dpe_etiquette_ges) : '',
                            'dpe_etiquette_conso' => isset($annonces->bien->diagnostiques->dpe_etiquette_conso) and get_option('dpe_etiquette_conso') !== false ? htmlspecialchars($annonces->bien->diagnostiques->dpe_etiquette_conso) : '',
                        ),
                    );

                    // Insérer la nouvelle annonce dans WordPress
                    $post_id = wp_insert_post($post_data);

                    $max_imag = get_option('max_image_import');
                    $imag_count = 0;
                    //insérer les photos de l'annonce
                    if (isset($annonces->photos)) {
                        foreach ($annonces->photos as $photo_url) {
                            foreach ($photo_url as $url) {
                                $base_url = strtok($url, '?');
                                $photo_id = $this->insert_attachment_from_url($base_url, $post_id);
                                if ($photo_id) {
                                    update_post_meta($post_id, '_thumbnail_id', $photo_id);
                                }
                            }

                            $imag_count++;

                            if ($imag_count >= $max_imag) {
                                break;
                            }
                        }
                    }

                    // Incrémenter le compteur de boucles
                    $loop_count++;

                    // Vérifier si le nombre maximum de boucles est atteint
                    if ($loop_count >= $max_loops) {
                        break;
                    }
                } else {
                    // L'annonce existe déjà, on passe à l'annonce suivante
                    continue;
                }
            }
            return $post_id;
        }
    }

    private function insert_attachment_from_url($url, $parent_post_id = null)
    {
        require_once ABSPATH . '/wp-admin/includes/media.php';
        require_once ABSPATH . '/wp-admin/includes/file.php';
        require_once ABSPATH . '/wp-admin/includes/image.php';

        // Télécharger la pièce jointe depuis l'URL
        $tmp = download_url($url);

        // Obtenir le type de fichier et le nom de fichier
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches);
        if (!$matches) {
            return false;
        }
        $file_array = array(
            'name' => basename($matches[0]),
            'tmp_name' => $tmp
        );

        // Insérer la pièce jointe dans la bibliothèque de médias
        $attachment_id = media_handle_sideload($file_array, $parent_post_id);

        // Supprimer le fichier temporaire
        //   unlink($file_array['tmp_name']);

        return $attachment_id;
    }

    public function slug_categorie($cat_type)
    {
        if (!empty($cat_type)) {
            switch ($cat_type) {
                case "V":
                case "M":
                    $title = "Vente";
                    break;

                case "L":
                    $title = "Location";
                    break;

                case "F":
                    $title = "Fonds de commerce";
                    break;

                case "S":
                    $title = "Location saisonnière";
                    break;

                case "B":
                    $title = "Cession de bail";
                    break;

                case "W":
                    $title = "Vente de viager";
                    break;

                case "J":
                    $title = "Coworkings";
                    break;

                case "I":
                    $title = "Enchères";
                    break;

                case "G":
                    $title = "Vente de neuf";
                    break;

                default:
                    $title = "Autres";
                    break;
            }
        }

        // Générer le slug à partir du titre
        $slug = sanitize_title($title);

        $cat_data = array(
            'titre' => $title,
            'slug'  => $slug
        );
        return $cat_data;
    }

    public function get_category_id($cat_type)
    {
        $cat_data = $this->slug_categorie($cat_type);

        // Vérifier si la catégorie existe déjà
        $category_term = term_exists($cat_data['titre'], 'category');
        if (!$category_term) {
            // Si la catégorie n'existe pas, la créer
            $category_term = wp_insert_term($cat_data['titre'], 'category', array(
                'slug' => $cat_data['slug'],
            ));
        }

        return $category_term['term_id'];
    }

    public function get_reg_and_dep_from_postal_code($code_postal)
    {
        //fichier departement et région
        $json_data = file_get_contents("$this->plugin_path/Inc/Ctr/Tools/departements-region.json");

        $departements_array = json_decode($json_data, true); // décoder le fichier JSON en tableau associatif

        foreach ($departements_array as $dep) {
            if ($dep['num_dep'] == substr($code_postal, 0, 2)) { // vérifier si le numéro de département correspond aux deux premiers chiffres du code postal
                $dep_name = $dep['dep_name'];
                $region_name = $dep['region_name'];
                return array('region' => $region_name, 'department' => $dep_name);
                break; // sortir de la boucle dès que les valeurs sont trouvées
            }
        }
    }

	public function get_reg_and_dep_taxonomies_id($code_postal)
	{
		// Récupérer la région et le département correspondant au code postal
		$data_location = $this->get_reg_and_dep_from_postal_code($code_postal);

		$region = $data_location['region'];
		$department = $data_location['department'];

		$localisation_taxonomy = 'localisation';
		
		// Vérifier si la région existe déjà et la récupérer ou la créer
		$region_term = term_exists($region, $localisation_taxonomy);
		if (is_wp_error($region_term)) {
			// Gérer l'erreur si term_exists renvoie un objet WP_Error
			return array('error' => 'Erreur lors de la vérification de la région');
		}
		
		if (!$region_term) {
			$region_term = wp_insert_term($region, $localisation_taxonomy);
			if (is_wp_error($region_term)) {
				// Gérer l'erreur si wp_insert_term renvoie un objet WP_Error
				return array('error' => 'Erreur lors de la création de la région');
			}
		}

		// Vérifier si le département existe déjà et le récupérer ou le créer comme sous-catégorie de la région
		$department_term = term_exists($department, $localisation_taxonomy, $region_term['term_id']);
		if (is_wp_error($department_term)) {
			// Gérer l'erreur si term_exists renvoie un objet WP_Error
			return array('error' => 'Erreur lors de la vérification du département');
		}

		if (!$department_term) {
			$department_term = wp_insert_term($department, $localisation_taxonomy, array(
				'parent' => $region_term['term_id'],
			));
			if (is_wp_error($department_term)) {
				// Gérer l'erreur si wp_insert_term renvoie un objet WP_Error
				return array('error' => 'Erreur lors de la création du département');
			}
		}

		// Retourner les identifiants des termes de taxonomie créés
		return array(
			'region_id' => $region_term['term_id'],
			'department_id' => $department_term['term_id'],
		);
	}
	
	// Planification de la tâche cron
    public function schedule_cron_create_post($url, $id)
    {
        $interval = get_the_author_meta('interval', $id);

        if (!wp_next_scheduled('ubimmo_cron_job', array($url, $id))) {
            wp_schedule_event(time(), $interval, 'ubimmo_cron_job', array($url, $id));
        }
    }
	
	// Fonction exécutée par la tâche cron
    public function ubimmo_do_cron_job($url, $id)
    {

	   $this->create_post($url, $id);
		$this->ubimmo_supprimer_cron_job($url, $id);
		$this->schedule_cron_create_post($url, $id);
    }

    // Nettoyage de la tâche cron
    public function ubimmo_supprimer_cron_job($url, $id)
    {
        if (wp_next_scheduled('ubimmo_cron_job', array($url, $id))) {
            wp_clear_scheduled_hook('ubimmo_cron_job', array($url, $id));
        }
    }
}
