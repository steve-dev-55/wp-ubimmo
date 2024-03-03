<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Api;

use Ubi\Base\SettingsApi;
use Ubi\Base\BaseController;
use Ubi\Api\ImportController;

/**
 * Code pour gérer les tâches planifiées
 */
class CronController extends BaseController
{
    public $settings;

    public $importController;

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->importController = new ImportController();

        add_action('ubimmo_cron_job', array($this, 'ubimmo_do_cron_job'));
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
        $this->importController->create_post($url, $id);
    }

    // Nettoyage de la tâche cron
    public function ubimmo_supprimer_cron_job($url, $id)
    {
        if (wp_next_scheduled('ubimmo_cron_job', array($url, $id))) {
            wp_clear_scheduled_hook('ubimmo_cron_job', array($url, $id));
        }
    }
}
