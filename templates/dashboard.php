<div class="wrap">
    <h1>Tableau de bord d'annonces immobilières</h1>
    <br>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Activités</a></li>
        <li><a href="#tab-2">A propos</a></li>
        <li><a href="#tab-3">Aide Ubimmo</a></li>
        <li><a href="#tab-4">Shortcode Ubimmo</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <h3>Activités des Agents Immobiliers Actifs</h3>
            <br>
            <table class="cpt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th class="text-center">Nombre d'annonces</th>
                        <th class="text-center">Dernière publication</th>
                        <th class="text-center">Prochaine publication</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($liste_annonceurs))
                        // Tri des utilisateurs en fonction de leur prochaine exécution planifiée
                        usort($liste_annonceurs, function ($a, $b) {
                            $a_next_cron_date = wp_next_scheduled('ubimmo_cron_job', array(get_the_author_meta('url_xml', $a->id), $a->id));
                            $b_next_cron_date = wp_next_scheduled('ubimmo_cron_job', array(get_the_author_meta('url_xml', $b->id), $b->id));
                            return ($a_next_cron_date < $b_next_cron_date) ? -1 : 1;
                        });

                    foreach ($liste_annonceurs as $annonceur_list) :
                        if ($this->do_check_annonceur($annonceur_list)) {
                            $id = $annonceur_list->id;
                            $pub_interval = get_the_author_meta('interval', $id);
                            $url_xml = get_the_author_meta('url_xml', $id);
                            //recupérer la date de l'execution du cron
                            $cron_schedule = wp_next_scheduled('ubimmo_cron_job', array($url_xml, $id));
                            //recupérer l'interval d'exécution
                            $interval = wp_get_schedule('ubimmo_cron_job', array($url_xml, $id));
                            switch ($interval) {
                                case 'hourly':
                                    $t_interval = '-1 hour';
                                    break;
                                case 'daily':
                                    $t_interval = '-1 day';
                                    break;
                                case 'weekly':
                                    $t_interval = '-1 week';
                                    break;
                            }
                            //recupérer la date de la précédente exécution a partir de l'interval
                            $date_precedente = isset($t_interval) ? date('d-m-y H:i:s', strtotime($t_interval, $cron_schedule)) : '';
                            $nbr_annonces = count_user_posts($id, 'biens_immobiliers');
                    ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $annonceur_list->last_name; ?></td>
                                <td class="text-center"><?php echo $nbr_annonces ?></td>
                                <td class="text-center"><?php echo $date_precedente ?></td>
                                <td class="text-center"><?php echo date('d-m-y H:i:s', $cron_schedule) ?></td>
                            </tr>
                        <?php  } ?>

                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th class="text-center">Nombre d'annonces</th>
                        <th class="text-center">Dernière publication</th>
                        <th class="text-center">Prochaine publication</th>
                    </tr>
                </tfoot>
            </table>

        </div>

        <div id="tab-2" class="tab-pane">
            <!DOCTYPE html>
            <html>

            <head>
                <title>À propos d'Ubimmo - Plugin WordPress pour importer des annonces immobilières sur Ubiflow</title>
            </head>

            <body>
                <h1>À propos d'Ubimmo</h1>
                <p>Ubimmo est un plugin WordPress qui permet d'importer automatiquement des annonces immobilières sur Ubiflow. Le plugin est conçu pour les professionnels de l'immobilier qui souhaitent gagner du temps en important leurs annonces sur Ubiflow sans avoir à les saisir manuellement.</p>
                <h2>Fonctionnalités</h2>
                <ul>
                    <li>Importation automatique des annonces immobilières sur Ubiflow</li>
                    <li>Création automatique de catégories pour les annonces</li>
                    <li>Localisation des annonces</li>
                    <li>Personnalisation des paramètres d'importation</li>
                </ul>
                <h2>Pourquoi choisir Ubimmo ?</h2>
                <p>Ubimmo est le choix idéal pour les professionnels de l'immobilier qui veulent gagner du temps en important leurs annonces sur Ubiflow. Le plugin est facile à utiliser et offre une grande flexibilité en termes de personnalisation des paramètres d'importation. Avec Ubimmo, vous pouvez importer vos annonces sur Ubiflow en quelques clics, ce qui vous permet de vous concentrer sur d'autres aspects de votre entreprise.</p>
                <h2>Contactez-nous</h2>
                <p>Pour plus d'informations sur Ubimmo ou pour obtenir de l'aide pour l'installation et la configuration du plugin, n'hésitez pas à nous contacter :</p>
                <ul>
                    <li>Téléphone : 01 23 45 67 89</li>
                    <li>E-mail : contact@ubimmo.com</li>
                    <li>Adresse : 123 rue du Commerce, 75001 Paris</li>
                </ul>
            </body>

            </html>
        </div>

        <div id="tab-3" class="tab-pane">
            <!DOCTYPE html>
            <html>

            <head>
                <title>Aide - Plugin WordPress Ubimmo</title>
            </head>

            <body>
                <h1>Aide - Plugin Ubimmo</h1>
                <h2>Installation</h2>
                <p>Pour installer le plugin Ubimmo, suivez les étapes suivantes :</p>
                <ol>
                    <li>Téléchargez le fichier zip du plugin depuis notre site web.</li>
                    <li>Connectez-vous à votre tableau de bord WordPress.</li>
                    <li>Cliquez sur "Extensions" dans le menu latéral.</li>
                    <li>Cliquez sur "Ajouter" en haut de la page.</li>
                    <li>Sélectionnez "Téléverser une extension".</li>
                    <li>Cliquez sur "Choisir un fichier" et sélectionnez le fichier zip du plugin Ubimmo.</li>
                    <li>Cliquez sur "Installer maintenant".</li>
                    <li>Une fois l'installation terminée, cliquez sur "Activer" pour activer le plugin.</li>
                </ol>

                <h2>Configuration</h2>
                <p>Pour configurer le plugin Ubimmo, suivez les étapes suivantes :</p>
                <ol>
                    <li>Cliquez sur "Ubimmo" dans le menu latéral de votre tableau de bord WordPress.</li>
                    <li>Accédez à la section "Réglages" pour définir les options d'importation des annonces.</li>
                    <li>Dans la section "Catégories", vous pouvez personnaliser les catégories utilisées pour classer les annonces importées.</li>
                    <li>Dans la section "Localisation", vous pouvez configurer les options de localisation des annonces.</li>
                </ol>

                <h2>Importation des annonces</h2>
                <p>Pour importer les annonces sur Ubiflow, suivez les étapes suivantes :</p>
                <ol>
                    <li>Cliquez sur "Ubimmo" dans le menu latéral de votre tableau de bord WordPress.</li>
                    <li>Accédez à la section "Agents Immobiliers" pour ajouter le login(Nom) le lien d'importation(xml) et la fréquence d'importation.</li>
                    <li>Cliquez sur "Activer" pour commencer le processus d'importation.</li>
                </ol>

                <h2>Besoin d'aide supplémentaire ?</h2>
                <p>Si vous avez besoin d'une assistance supplémentaire pour l'installation, la configuration ou l'utilisation du plugin Ubimmo, n'hésitez pas à nous contacter :</p>
                <ul>
                    <li>E-mail : djprince2000@gmail.com</li>
                </ul>

            </body>

            </html>
        </div>
        <div id="tab-4" class="tab-pane">
            <!DOCTYPE html>
            <html>

            <head>
                <title>Aide Shortcode pour les Annonces Immobilières</title>
            </head>

            <body>
                <h1>Aide Shortcode pour les Annonces Immobilières</h1>
                <p>Utilisez le shortcode suivant pour afficher une liste d'annonces immobilières :</p>
                <pre>[annonces_immobilieres]</pre>
                <p>Ce shortcode affichera une liste d'annonces immobilières sur la page où il est inséré. Vous pouvez personnaliser les paramètres du shortcode pour afficher des annonces spécifiques ou appliquer des filtres selon vos besoins.</p>
                <h2>Exemples d'utilisation :</h2>
                <ul>
                    <li>Utilisez <code>[annonces_immobilieres]</code> pour afficher toutes les annonces disponibles.</li>
                    <li>Utilisez <code>[annonces_immobilieres type="maison"]</code> pour afficher uniquement les annonces de type "appartement".</li>
                    <li>Utilisez <code>[annonces_immobilieres categorie="vente"]</code> pour afficher les annonces de la catégorie Vente.</li>
                    <li>Utilisez <code>[annonces_immobilieres ville="Paris"]</code> pour afficher les annonces situées à Paris.</li>

                </ul>
            </body>

            </html>
        </div>
    </div>
</div>