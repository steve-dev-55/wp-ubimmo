<table class="form-table">
    <tbody>
        <?php
        //caractéristique du bien
        echo $this->generate_text_input('Nombre de pièces', 'nb_pieces', get_post_meta($post->ID, 'nb_pieces', true));
        echo $this->generate_text_input('Nombre de chambres', 'nb_chambres', get_post_meta($post->ID, 'nb_chambres', true));
        echo $this->generate_text_input('surface', 'surface', get_post_meta($post->ID, 'surface', true));
        echo $this->generate_text_input('surface habitable', 'surface_habitable', get_post_meta($post->ID, 'surface_habitable', true));
        echo $this->generate_text_input('prix', 'prix', get_post_meta($post->ID, 'prix', true));
        echo $this->generate_checkbox_input('Garage', 'garage', get_post_meta($post->ID, 'garage', true));
        echo $this->generate_checkbox_input('Terrasse', 'terrasse', get_post_meta($post->ID, 'terrasse', true));

        ?>
    </tbody>
</table>