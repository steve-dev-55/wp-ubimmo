<table class="form-table">
    <tbody>
        <?php
        //localisation du bien
        echo $this->generate_text_input('ville', 'ville', get_post_meta($post->ID, 'ville', true));
        echo $this->generate_text_input('code_postal', 'code_postal', get_post_meta($post->ID, 'code_postal', true));

        ?>
    </tbody>
</table>