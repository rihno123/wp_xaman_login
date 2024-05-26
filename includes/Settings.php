<?php
function xrpl_token_menu() {
    add_menu_page('Plugin Settings', 'XRPL login', 'manage_options', 'XRPL-login', 'Plugin_menu', '', 100);
}


function Plugin_menu() {
    if (!current_user_can('manage_options')) {
        return;
    }
    echo "<h2>" . __( 'Settings', 'menu-test' ) . "</h2>";
    echo "<p>This is a settings page for plugin.</p>";
    // Check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // Add settings saved message with the class of "updated"
        add_settings_error('save_messages', 'save_message', __('Settings Saved', 'settings'), 'updated');
    }

        // Register a new setting for "my_custom_plugin" page
        ?>
        <div class="wrap">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            <form id = "Form" action="options.php" method="post">
                <?php
                // Output security fields for the registered setting "settings"
                settings_fields('settings');
                // Output setting sections and their fields
                // (sections are registered for "settings", each field is registered to a specific section)
                do_settings_sections('settings');
                // Output save settings button
                submit_button('Save Settings');
                ?>
                <label for="XUMM_SECRET" style="font-size: 1.5em; margin-right: 14px;" >Xaman API Secret Key</label>
                <input type="text" size="40" id="XUMM_SECRET" name="XUMM_SECRET" value="<?php echo esc_attr(get_option('XUMM_SECRET')); ?>" /><br><br><br>
                <label for="XUMM_KEY" style="font-size: 1.5em; margin-right: 70px;" >Xaman API Key </label>
                <input type="text" size="40" id="XUMM_KEY" name="XUMM_KEY" value="<?php echo esc_attr(get_option('XUMM_KEY')); ?>" /><br><br><br>

            </form>
        </div>
        <?php


}

function Settings_saved()
{
register_setting('settings', 'XUMM_KEY');
register_setting('settings', 'XUMM_SECRET');

}

add_action('admin_init', 'Settings_saved');
add_action('admin_menu', 'xrpl_token_menu');