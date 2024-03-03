<table class="form-table">
    <tbody>
        <?php
        //diagnostiques energetique
        $energ_data = array('', 'A', 'B', 'C', 'D', 'E', 'F', 'G');
        echo $this->generate_select_input(' diagnostic d\'émission de gaz / CO2 ', 'dpe_etiquette_ges', $energ_data, get_post_meta($post->ID, 'dpe_etiquette_ges', true));
        echo $this->generate_select_input('Diagnostic de consommation énergétique ', 'dpe_etiquette_conso', $energ_data, get_post_meta($post->ID, 'dpe_etiquette_conso', true));
        ?>
    </tbody>
</table>