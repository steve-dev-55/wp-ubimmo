<?php

/**
 * @package  Ubimmo
 */

namespace Inc\Ctr;

use Inc\Base\SettingsApi;
use Inc\Ctr\CronController;
use Inc\Base\BaseController;
use Inc\Ctr\ImportController;


/**
 * Code pour gérer les annonceurs
 */
class AnnonceursController extends BaseController
{
    public $settings;

    public $subpages = array();

    public $annonceur_datas = array();

    public $importController;

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->setSubpages();

        $this->enregitrer_annonceurs();

        $this->importController = new ImportController();

        $this->settings->addSubPages($this->subpages)->register();

        if (!empty($this->annonceur_datas)) {
            add_action('init', array($this, 'traiter_formulaire_annonceur'));
        }
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'ubi_immo',
                'page_title' => 'Agents Immobiliers',
                'menu_title' => 'Agents Immobiliers',
                'capability' => 'manage_options',
                'menu_slug' => 'ubi_agents',
                'callback' => array($this, 'afficher_page_annonceur')
            )
        );
    }

    // Fonction de rappel pour afficher la page "Annonceur"
    public function afficher_page_annonceur()
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

        // Inclure la vue pour afficher la liste des annonceurs
        include("$this->plugin_path/templates/annonceurs.php");
    }

    // Enregistrer les annonceurs
    public function enregitrer_annonceurs()
    {
        if (!empty($_POST)) {

            // Récupérer les valeurs soumises dans le formulaire
            $action = isset($_POST['action']) ? $_POST['action'] : '';

            if ($action === 'creer_annonceur' or $action === 'modifier_annonceur' or $action === 'supprimer_annonceur' or $action === 'activer_annonceur') {

                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $nom = isset($_POST['nom']) ? sanitize_text_field($_POST['nom']) : '';
                $url = isset($_POST['url']) ? sanitize_text_field($_POST['url']) : '';
                $interval = isset($_POST['interval']) ? sanitize_text_field($_POST['interval']) : '';
                $activation = isset($_POST['activation']) ? intval($_POST['activation']) : 0;

                $this->annonceur_datas[] = array(
                    'id'   => $id,
                    'nom' => $nom,
                    'url'   => $url,
                    'interval'   => $interval,
                    'activation'   => $activation,
                    'action'   => $action,
                );
            }
        }
    }

    //sauvegarder les éléments de l'annoceur
    public function traiter_formulaire_annonceur()
    {
        foreach ($this->annonceur_datas as $annonceur_data) {
            // Créer ou modifier l'annonceur en fonction de l'action soumise dans le formulaire
            switch ($annonceur_data['action']) {
                case 'creer_annonceur':
                    // Vérifier si les champs sont non vides
                    if (empty($annonceur_data['nom']) || empty($annonceur_data['url'])) {
                        wp_die('Les champs ne peuvent pas être vides.');
                    }

                    $password = wp_generate_password(12, false);

                    $user_data = [
                        'user_login' => $annonceur_data['nom'],
                        'user_pass'  => $password,
                        'role'       => 'author',
                        'meta_input' => array(
                            'url_xml'  => $annonceur_data['url'],
                            'interval'  => $annonceur_data['interval']
                        )

                    ];

                    $annonceur_id = wp_insert_user($user_data);
                    // success
                    if (!is_wp_error($annonceur_id)) {
                        $this->display_message('Agent enregistré!');
                    } else {
                        $message = 'Une erreur est survenue lors de l\'opération.';
                        $this->display_message($message, 'error');
                    }
                    break;
                case 'modifier_annonceur':

                    $user_data = [
                        'ID'       => $annonceur_data['id'],
                        'user_login' => $annonceur_data['nom'],
                        'meta_input' => array(
                            'url_xml'  => $annonceur_data['url'],
                            'interval'  => $annonceur_data['interval']
                        )

                    ];

                    $annonceur_id = wp_update_user($user_data);

                    // success
                    if (!is_wp_error($annonceur_id)) {
                        $this->display_message('Agent mis a jour!');
                    } else {
                        $message = 'Une erreur est survenue lors de l\'opération.';
                        $this->display_message($message, 'error');
                    }
                    break;
                case 'supprimer_annonceur':
                    require_once(ABSPATH . 'wp-admin/includes/user.php');
                    $reponse = wp_delete_user($annonceur_data['id']);

                    if (($reponse === true)) {
                        $this->display_message('Agent suprimé!');
                    } else {
                        $message = 'Une erreur est survenue lors de l\'opération.';
                        $this->display_message($message, 'error');
                    }
                    break;
                case 'activer_annonceur':
                    //activer l'utilisateur
                    wp_update_user(
                        array(
                            'ID'       => $annonceur_data['id'],
                            'meta_input' => array(
                                'activation'  => $annonceur_data['activation']
                            )
                        )
                    );
                    if ($annonceur_data['activation'] == 1) {
                        //mettre a jour les infos annonceurs
                        $this->importController->update_user($annonceur_data['url'], $annonceur_data['id']);

                        //charger les premières annonces
                        $this->importController->create_post($annonceur_data['url'], $annonceur_data['id']);

                        //créer la tache cron
                        $this->importController->schedule_cron_create_post($annonceur_data['url'], $annonceur_data['id']);
                    } else {
                        //supprimer la tache cron si existe
                        $this->importController->ubimmo_supprimer_cron_job($annonceur_data['url'], $annonceur_data['id']);
                    }

                    break;
                default:
                    wp_die('Action invalide.');
                    break;
            }
            $annonceur_url = admin_url('admin.php?page=ubi_agents');
            header('Location:' . $annonceur_url);
            exit;
        }
    }
}
