<div class="wrap">
    <h2><?=__('Eventkrake Settings', 'g4rf-eventkrake')?></h2>

    <form action="options.php" method="post">
        <?php settings_fields('eventkrake-settings'); ?>
        <?php do_settings_sections('eventkrake'); ?>
 
        <?php submit_button(); ?>
    </form>
</div>

