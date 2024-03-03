<div class="wrap">
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