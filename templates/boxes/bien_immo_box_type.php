        <table class="form-table">
            <tbody>
                <?php
                // Récupérer les métadonnées existantes
                //type de bien
                echo $this->generate_text_input('Type de mandat', 'mandat_type', get_post_meta($post->ID, 'mandat_type', true));
                echo $this->generate_text_input('Référence ', 'reference', get_post_meta($post->ID, 'reference', true));
                $ype_prestation_data = array('Vente', 'Location', 'Location saisonnière', 'Fond de commerce', 'Cession de bail', 'Enchères');
                echo $this->generate_select_input('Type de prestation ', 'type_prestaton', $ype_prestation_data, get_post_meta($post->ID, 'type_prestaton', true));
                ?>
            </tbody>
        </table>