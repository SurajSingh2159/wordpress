<?php
/**
 * Plugin Name:	EnvoThemes Demo Import
 * Description:	Import EnvoThemes official themes demo content, widgets and theme settings with just one click.
 * Version: 1.0.4
 * Author: EnvoThemes
 * Author URI: https://envothemes.com
 * Text Domain: envothemes-demo-import
 * Domain Path: /languages/
 *
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Returns the main instance of EnvoThemes_Demo_Import to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object EnvoThemes_Demo_Import
 */
function EnvoThemes_Demo_Import() {
    return EnvoThemes_Demo_Import::instance();
}

// End EnvoThemes_Demo_Import()

EnvoThemes_Demo_Import();

/**
 * Main EnvoThemes_Demo_Import Class
 *
 * @class EnvoThemes_Demo_Import
 * @version	1.0.0
 * @since 1.0.0
 * @package	EnvoThemes_Demo_Import
 */
final class EnvoThemes_Demo_Import {

    /**
     * EnvoThemes_Demo_Import The single instance of EnvoThemes_Demo_Import.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;

    // Admin - Start

    /**
     * The admin object.
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $admin;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($widget_areas = array()) {
        $this->token = 'envothemes-demo-import';
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->version = '1.0';

        define('ENVO_URL', $this->plugin_url);
        define('ENVO_PATH', $this->plugin_path);
        define('ENVO_VERSION', $this->version);
        define('ENVO_FILE_PATH', __FILE__);
        define('ENVO_ADMIN_PANEL_HOOK_PREFIX', 'theme-panel_page_envothemes-panel');


        register_activation_hook(__FILE__, array($this, 'install'));

        add_action('init', array($this, 'load_plugin_textdomain'));
        
        $theme = wp_get_theme();
        if ('Envo eCommerce' == $theme->name || 'envo-ecommerce' == $theme->template || 'Envo Storefront' == $theme->name || 'envo-storefront' == $theme->template) {
            require_once( ENVO_PATH . 'includes/panel/demos.php' );
            require_once( ENVO_PATH . 'includes/wizard/wizard.php' );
            require_once( ENVO_PATH . 'includes/notify/notify.php' );
        }
        
    }

    /**
     * Main EnvoThemes_Demo_Import Instance
     *
     * Ensures only one instance of EnvoThemes_Demo_Import is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see EnvoThemes_Demo_Import()
     * @return Main EnvoThemes_Demo_Import instance
     */
    public static function instance() {
        if (is_null(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

// End instance()

    /**
     * Load the localisation file.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('envothemes-demo-import', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0.0');
    }

    /**
     * Installation.
     * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install() {
        $this->_log_version_number();
    }

    /**
     * Log the plugin version number.
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number() {
        // Log the version number.
        update_option($this->token . '-version', $this->version);
    }

}

// End Class

/**
 * Add Metadata on plugin activation.
 */
function envo_extra_activate() {
    add_site_option('envothemes_active_time', time());
    add_site_option('envothemes_active_pro_time', time());
}

register_activation_hook(__FILE__, 'envo_extra_activate');

/**
 * Remove Metadata on plugin Deactivation.
 */
function envo_extra_deactivate() {
    delete_option('envothemes_active_time');
    delete_option('envothemes_maybe_later');
    delete_option('envothemes_review_dismiss');
    delete_option('envothemes_active_pro_time');
}

register_deactivation_hook(__FILE__, 'envo_extra_deactivate');