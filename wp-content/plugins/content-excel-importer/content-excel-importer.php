<?php
/*
 * Plugin Name: Import Content in WordPress & WooCommerce with Excel
 * Plugin URI: https://extend-wp.com/content-excel-importer-for-wordpress/
 * Description: Import Posts, Pages, Simple Products for WooCommerce & Wordpress with Excel. Migrate Easily. No more CSV Hassle
 * Version: 3
 * Author: extendWP
 * Author URI: https://extend-wp.com
 *
 * WC requires at least: 2.2
 * WC tested up to: 4.1
 * 
 * License: GPL2s
 * Created On: 04-06-2018
 * Updated On: 08-04-2020
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( plugin_dir_path(__FILE__) .'/query_class.php');
include( plugin_dir_path(__FILE__) .'/content-excel-importer_content.php');



function load_contentExceIimporter_js(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
	
	//ENQUEUED CSS FILE INSTEAD OF INLINE CSS
	wp_enqueue_style( 'contentExceIimporter_css', plugins_url( "/css/contentExceIimporter.css", __FILE__ ) );	
	wp_enqueue_style( 'contentExceIimporter_css');		

	
    wp_enqueue_script( 'contentExceIimporter_js', plugins_url( '/js/contentExceIimporter.js?v=1234', __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-draggable','jquery-ui-droppable') , null, true);		
	wp_enqueue_script( 'contentExceIimporter_js');

    $cei = array( 
		'RestRoot' => esc_url_raw( rest_url() ),
		'plugin_url' => plugins_url( '', __FILE__ ),
		'siteUrl'	=>	site_url(),
		'nonce' => wp_create_nonce( 'wp_rest' ),
		'ajax_url' => admin_url( 'admin-ajax.php' ),		
	);
	
    wp_localize_script( 'contentExceIimporter_js', 'contentExcelImporter', $cei );
	
}
add_action('admin_enqueue_scripts', 'load_contentExceIimporter_js');

add_action( 'wp_ajax_import_content', 'import_content' );
add_action( 'wp_ajax_nopriv_import_content',  'import_content' );



//ADD MENU LINK AND PAGE FOR WOOCOMMERCE IMPORTER
add_action('admin_menu', 'contentExceIimporter_menu');

function contentExceIimporter_menu() {

	add_menu_page('Content Excel Importer Settings', 'Content Excel Importer', 'administrator', 'content-excel-importer', 'contentExceIimporter_init', 'dashicons-upload','50');
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_contentExceIimporter_links' );

function add_contentExceIimporter_links ( $links ) {
 $links[] =  '<a href="' . admin_url( 'admin.php?page=content-excel-importer' ) . '">Settings</a>';
 $links[] = '<a href="https://extend-wp.com/product/content-importer-wordpress-woocommerce-excel/" target="_blank">PRO Version</a>';
 $links[] = '<a href="https://extend-wp.com" target="_blank">More plugins</a>';
   return $links;
}

function contentExceIimporter_main() { ?>

		
		<div class='left_wrap' >	
		
			<div class='premium_msg'>
				<p>
					<strong>
					Only available on PRO Version <a class='premium_button' target='_blank'  href='https://extend-wp.com/product/content-importer-wordpress-woocommerce-excel/'>Get it Here</a>
					</strong>
				</p>
			</div>
			<div class='freeContent'>
			<?php
				$products = new contentExcelImporterProducts;
				$products->importProductsDisplay();
			?>
			</div>
		</div>
		
		<div class='right_wrap rightToLeft'>
			<h2  class='center'>NEED MORE FEATURES? </h2>
				<ul>
					<li> - Import Any Custom Post Type</li>
					<li> - Import Any Category / Custom Taxonomy with Excel </li>
					<li> - Delete Category / Custom Taxonomy with Excel </li>
					<li> - Import Featured Image along with Post</li>
					<li> - Import Variable Woocommerce Products</li>
					<li> - Import Product Featured Image from URL</li>
					<li> - NEW! Import Product Gallery Images from URL!</li>
					<li> - NEW! Define Downloadable, name, URL for file, expiry date & limit!</li>
					<li> - Import Category Term Description with HTML Support</li>
				</ul>	
			<p class='center'>			
				<a target='_blank'  href='https://extend-wp.com/product/content-importer-wordpress-woocommerce-excel/'>
					<img class='premium_img' src='<?php echo plugins_url( 'images/content-excel-importer-pro.png', __FILE__ ); ?>' alt='Content Excel Importer PRO' title='Content Excel Importer PRO' />
				</a>
			<p  class='center'>
				<a class='premium_button' target='_blank'  href='https://extend-wp.com/product/content-importer-wordpress-woocommerce-excel/'>
					<?php _e("Get it here","contentExceIimporter");?>	
				</a>
			</p>
		</div>
<?php		
}


function contentExceIimporter_init() {
	contentExceIimporter_form_header();
	?>
	<div class="content-excel-importer" >	
<div class='msg'></div>

	<h2 class="nav-tab-wrapper">
		<a class='nav-tab nav-tab-active' href='?page=content-excel-importer'>Import Content</a>
		<a class='nav-tab premium' href='#'>Delete Content</a>
		<a class='nav-tab premium' href='#'>Import Categories</a>
		<a class='nav-tab premium' href='#'>Delete Categories</a>
	</h2>	
	
	<?php
		contentExceIimporter_main();
	
	
	?>	
	
	</div>
	
	
	<?php
	contentExceIimporter_form_footer();
}

function contentExceIimporter_form_header() {
?>
	<h2><img src='<?php echo plugins_url( 'images/content-excel-importer-horizontal.png', __FILE__ ); ?>' style='width:100%' />
<?php
}

function contentExceIimporter_form_footer() {
?>
	<hr>
				

<?php
}