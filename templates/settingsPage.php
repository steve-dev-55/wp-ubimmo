<div class="wrap">
<img src="<?php echo plugins_url( 'ubimmo/templates/images/logo-ubimmo.jpg' ); ?>" alt="Logo ubimmo" style="position: absolute; top: 0; right: 0; max-width:192px;">
    <div class="tab-content">
        <div class="tab-pane active">
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php settings_fields('ubimmo_settings');
                do_settings_sections('ubimmo_reglage');
                submit_button(); ?>
            </form>
        </div>
    </div>
</div>