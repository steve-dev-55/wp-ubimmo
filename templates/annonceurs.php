<div class="wrap">
    <h1>Agents Immobiliers</h1>
    <?php settings_errors(); ?>
    <br>
    <ul class="nav nav-tabs">
        <li class="<?php echo !(isset($_GET['action']) && $_GET['action'] == 'editer_annonceur') ? 'active' : '' ?>"><a href="#tab-1">Agents Immobiliers</a></li>
        <li class="<?php echo (isset($_GET['action']) && $_GET['action'] == 'editer_annonceur') ? 'active' : '' ?>">
            <a href="#tab-2">
                <?php echo (isset($_GET['action']) && $_GET['action'] == 'editer_annonceur')  ? 'Modifier' : 'Ajouter' ?> Agents Immobiliers
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane <?php echo !(isset($_GET['action']) && $_GET['action'] == 'editer_annonceur') ? 'active' : '' ?>">

            <h1>Liste des Agents Immobiliers</h1>
            <br>
            <table class="cpt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="text-center">Activer</th>
                        <th>Nom</th>
                        <th>Url</th>
                        <th class="text-center">Annonces</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($liste_annonceurs))
                        foreach ($liste_annonceurs as $annonceur_list) :
                            $url_xml = get_the_author_meta('url_xml', $annonceur_list->id);
                            $nbr_annonces = count_user_posts($annonceur_list->id, 'biens_immobiliers');
                            $activation = get_the_author_meta('activation', $annonceur_list->id);
                            $checked = ($activation == 1) ? 'checked' : '';
                    ?>
                        <tr>
                            <td><?php echo $annonceur_list->id; ?></td>
                            <td class="text-center">
                                <form method="post">
                                    <input type="hidden" name="action" value="activer_annonceur">
                                    <input type="hidden" name="id" value="<?php echo $annonceur_list->id; ?>">
                                    <input type="hidden" name="url" value="<?php echo $url_xml; ?>">
                                    <div class="ui-toggle mb-10"><input onchange="this.form.submit()" type="checkbox" id="activation<?php echo $annonceur_list->id; ?>" name="activation" value="1" class="" <?php echo $checked; ?>><label for="activation<?php echo $annonceur_list->id; ?>">
                                            <div></div>
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td><?php echo $annonceur_list->user_login; ?></td>
                            <td><?php echo $this->limit_text($url_xml, 80); ?></td>
                            <td class="text-center"><?php echo $nbr_annonces ?></td>
                            <td class="text-center">

                                <a class="button button-primary button-small" href="?page=ubi_agents&action=editer_annonceur&id=<?php echo $annonceur_list->id; ?>">Modifier</a>
                                <?php echo '<form method="post" class="inline-block">';
                                echo '<input type="hidden" name="action" value="supprimer_annonceur">';
                                echo '<input type="hidden" name="id" value="' . $annonceur_list->id . '">';
                                submit_button('Supprimer', 'delete small', 'submit', false, array(
                                    'onclick' => 'return confirm("vous allez supprimer ' . $annonceur_list->user_login . '? Les annonces associées a ce compte seront supprimé.");'
                                ));
                                echo '</form> ' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th class="text-center">Activer</th>
                        <th>Nom</th>
                        <th>Url</th>
                        <th class="text-center">Annonces</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </tfoot>
            </table>

        </div>

        <div id="tab-2" class="tab-pane <?php echo (isset($_GET['action']) && $_GET['action'] == 'editer_annonceur') ? 'active' : '' ?>">

            <?php if (isset($_GET['action']) && $_GET['action'] == 'editer_annonceur') : ?>
                <?php
                $id = $_GET['id'];
                $url_xml = get_the_author_meta('url_xml', $id);
                $interval = get_the_author_meta('interval', $id);
                $annonceur = get_user_by('id', $id);
                ?>
                <h2>Editer l'Agents Immobilier "<?php echo $annonceur->user_login; ?>"</h2>
                <form method="post">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="nom">Nom de l'agent:</label>
                                </th>
                                <td>
                                    <input type="text" class="regular-text" name="nom" value="<?php echo $annonceur->user_login; ?>" disabled="disabled">
                                    <p>Les identifiants ne peuvent pas être modifiés.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="url">Url xml d'importation:</label>
                                </th>
                                <td>
                                    <input type="text" class="regular-text" name="url" value="<?php echo $url_xml; ?>"><br>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="interval">Intervalle de publication:</label>
                                </th>
                                <td>
                                    <select name="interval" id="interval">
                                        <option value="hourly" <?php echo selected($interval, 'hourly'); ?>>Toutes les heures</option>
                                        <option value="daily" <?php echo selected($interval, 'daily'); ?>>Tous les jours</option>
                                        <option value="weekly" <?php echo selected($interval, 'weekly'); ?>>Toutes les semaines</option>
                                    </select>
                                    <input type="hidden" name="action" value="modifier_annonceur">
                                    <input type="hidden" name="id" value="<?php echo $annonceur->id; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button('Mettre à jour'); ?>
                </form>
            <?php else : ?>
                <h2>Ajouter un nouvel Agents immobilier</h2>
                <form method="post">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="nom">Nom de l'agent:</label>
                                </th>
                                <td>
                                    <input type="text" class="regular-text" name="nom" placeholder="ex. UBIMMO">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="url">Url xml d'importation:</label>
                                </th>
                                <td>
                                    <input type="text" class="regular-text" name="url" placeholder="ex. http://sw.ubiflow.net/diffusion-annonces.php....">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="interval">Intervalle de publication:</label>
                                </th>
                                <td>
                                    <select name="interval" id="interval">
                                        <option value="hourly">Toutes les heures</option>
                                        <option value="daily">Tous les jours</option>
                                        <option value="weekly">Toutes les semaines</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="action" value="creer_annonceur">
                    <?php submit_button('Enregistrer'); ?>
                </form>
            <?php endif; ?>
        </div>
    </div>