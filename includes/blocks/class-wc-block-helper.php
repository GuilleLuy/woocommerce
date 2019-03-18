<?php
/**
 * Handle deactivating older versions of the WooCommerce Blocks featured plugin.
 *
 * @package WooCommerce\Blocks
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Block_Helper Class.
 */
class WC_Block_Helper {

	/**
	 * Class instance.
	 *
	 * @var WC_Block_Helper instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'activated_plugin', array( __CLASS__, 'check_featured_plugin' ) );
		if ( defined( 'WGPB_VERSION' ) && version_compare( WGPB_VERSION, '1.4.0', '<=' ) ) {
			self::deactivate_plugin();
		}
	}

	/**
	 * Prevent reactivation of the featured plugin if version < 1.40
	 *
	 * @param string $plugin Path to activated plugin file, relative to WP_PLUGIN_DIR.
	 */
	public static function check_featured_plugin( $plugin ) {
		if ( false !== stripos( $plugin, 'woocommerce-gutenberg-products-block.php' ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			if ( version_compare( $plugin_data['Version'], '1.4.0', '<=' ) ) {
				deactivate_plugins( $plugin );
			}
		}
	}

	/**
	 * Attempt to deactivate the featured plugin.
	 */
	public static function deactivate_plugin() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			return;
		}

		$active_plugins = get_option( 'active_plugins', array() );
		foreach ( $active_plugins as $path ) {
			if ( false !== stripos( $path, 'woocommerce-gutenberg-products-block.php' ) ) {
				deactivate_plugins( $path );
			}
		}
	}
}

WC_Block_Helper::get_instance();
