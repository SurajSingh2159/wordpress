<?php
/*
 * Plugin Name: Product Excel Import & Bulk Edit for WooCommerce 
 * Plugin URI: https://extend-wp.com/product-bulk-edit-product-excel-importer-for-woocommerce/
 * Description: Bulk Product Editing for Simple WooCommerce Products & Import with Excel
 * Version: 2.1
 * Author: extendWP
 * Author URI: https://extend-wp.com
 *
 * WC requires at least: 2.2
 * WC tested up to: 4.1
 *  
 * License: GPL2
 * Created On: 09-11-2017
 * Updated On: 08-04-2020
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



include( plugin_dir_path(__FILE__) .'/bulk_edit_products.php');
include( plugin_dir_path(__FILE__) .'/excel_products.php');

function load_webd_woocommerce_product_excel_importer_bulk_edit_js(){
	
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-accordion');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
		
	if( ! wp_script_is( "webd_woocommerce_bulk_edit_fa", 'enqueued' ) ) {
		wp_enqueue_style( 'webd_woocommerce_bulk_edit_fa', plugins_url( '/css/font-awesome.min.css', __FILE__ ));	
	}
	
	//ENQUEUED CSS FILE INSTEAD OF INLINE CSS
	wp_enqueue_style( 'webd_woocommerce_product_excel_importer_bulk_edit_style_css', plugins_url( "/css/style.css", __FILE__ ) );	
	wp_enqueue_style( 'webd_woocommerce_product_excel_importer_bulk_edit_style_css');		
		
    wp_enqueue_script( 'webd_woocommerce_product_excel_importer_bulk_edit_style_js_excel', plugins_url( '/js/javascript_excel.js', __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-draggable','jquery-ui-droppable') , null, true);		
	wp_enqueue_script( 'webd_woocommerce_product_excel_importer_bulk_edit_style_js_excel');
    wp_enqueue_script( 'webd_woocommerce_product_excel_importer_bulk_edit_style_js_bulk_edit', plugins_url( '/js/javascript_bulk_edit.js', __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-draggable','jquery-ui-droppable') , null, true);		
	wp_enqueue_script( 'webd_woocommerce_product_excel_importer_bulk_edit_style_js_bulk_edit');	
    $woopeipurl = array( 
		'plugin_url' => plugins_url( '', __FILE__ ),
		'siteUrl'	=>	site_url(),
		'nonce' => wp_create_nonce( 'wp_rest' )		
	);
	
    wp_localize_script( 'webd_woocommerce_product_excel_importer_bulk_edit_style_js_excel', 'wpeip_url', $woopeipurl );

	
}
add_action('admin_enqueue_scripts', 'load_webd_woocommerce_product_excel_importer_bulk_edit_js');



//ADD MENU LINK AND PAGE FOR WOOCOMMERCE IMPORTER
add_action('admin_menu', 'webd_woocommerce_product_excel_importer_bulk_edit_menu');

function webd_woocommerce_product_excel_importer_bulk_edit_menu() {
	add_submenu_page( 'edit.php?post_type=product', 'Product Excel Importer & Bulk Editing', 'Product Excel Importer & Bulk Editing', 'manage_options', 'webd-woocommerce-product-excel-importer-bulk-edit', 'webd_woocommerce_product_excel_importer_bulk_edit_init' );	
	add_submenu_page( 'woocommerce', 'Product Excel Importer & Bulk Editing', 'Product Excel Importer & Bulk Editing', 'manage_options', 'webd-woocommerce-product-excel-importer-bulk-edit', 'webd_woocommerce_product_excel_importer_bulk_edit_init' );
	add_menu_page('Product Excel Importer & Bulk Editing Settings', 'Product Excel Importer & Bulk Editing', 'administrator', 'webd-woocommerce-product-excel-importer-bulk-edit', 'webd_woocommerce_product_excel_importer_bulk_edit_init', 'dashicons-edit','50');
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_webd_woocommerce_product_excel_importer_bulk_edit_links' );

function add_webd_woocommerce_product_excel_importer_bulk_edit_links ( $links ) {
 $links[] =  '<a href="' . admin_url( 'admin.php?page=webd-woocommerce-product-excel-importer-bulk-edit' ) . '">Settings</a>';
 $links[] = '<a href="https://extend-wp.com/product/woocommerce-product-excel-importer-bulk-editing-pro/" target="_blank">PRO Version</a>';
 $links[] = '<a href="https://extend-wp.com/" target="_blank">More plugins</a>';
   return $links;
}




function webd_woocommerce_product_excel_importer_bulk_edit_init() {
	
	$productsExcel = new WebdWoocommerceEProducts;
	$productsBulk = new WebdWoocommerceBProducts;
	
	webd_woocommerce_product_excel_importer_bulk_edit_form_header();
	?>
	<div class="excel_bulk_wrap_free" >
		<div class='left_wrap' >
			<div class='msg'></div>
			<?php 
			$tabs = array( 'main' => 'Import/Update - Excel','search-edit' => 'Search / Bulk Edit');
			if($_GET['tab'] ){
				$current = $_GET['tab'] ;
			}else $current = 'main';

			
			echo '<h2 class="nav-tab-wrapper" >';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class contant' href='?page=webd-woocommerce-product-excel-importer-bulk-edit&tab=$tab'>$name</a>";
			}?>
				<a class='nav-tab premium' href='#'>Export Products</a>
				<a class='nav-tab premium' href='#'>Delete Products</a>			
				<a class='nav-tab premium' href='#'>Import Categories</a>
				<a class='nav-tab premium' href='#'>Delete Categories</a>
				<a class='nav-tab  excel_bulk_wrap_free_instructionsVideo' href='#excel_bulk_wrap_free_instructionsVideo'>Instructions</a>
				<a class='nav-tab  gopro' href='#'>GO PRO</a>
			<?php
			echo '</h2>';
			
			?>
			<div class='premium_msg'>
				<p>
					<strong>
						Only available on Premium Version <a class='premium_button' target='_blank'  href='https://extend-wp.com/product/woocommerce-product-excel-importer-bulk-editing-pro/'>Get it Here</a>
						</strong>
					</p>
			</div>
			<div class='the_Content'>
			<?php
			if(isset ( $_GET['tab'] )  && $_GET['tab']=='search-edit'){
				$productsBulk->editProductsDisplay();			
			}else $productsExcel->importProductsDisplay(); ?>	
			</div>
		</div>	
		<div class='right_wrap rightToLeft'>	
					<p>
						<a target='_blank'  href='https://extend-wp.com/product/woocommerce-product-excel-importer-bulk-editing-pro/'>
							<img class='premium_img' src='<?php echo plugins_url( 'images/webd_woocommerce_product_excel_importer_bulk_edit_pro.png', __FILE__ ); ?>' alt='Woocommerce Product Excel and Bulk Editing Pro' title='Woocommerce Product Excel and Bulk Editing Pro' />
						</a>
					</p>

					<div>
						<p><i class='fa fa-check'></i> Advanced Search  - By any Taxonomy</p>					
						<p><i class='fa fa-check'></i> Bulk Edit Variable Products and Variations</p>
						<p><i class='fa fa-check'></i> Bulk Edit Support for Product Taxonomies, Custom Taxonomies</p>
						<p><i class='fa fa-check'></i> Import Simple and Variable Products with Excel</p>
						<p><i class='fa fa-check'></i> NEW - Import / Update Simple Products unlimited Attributes Comma Separated!</p>
						<p><i class='fa fa-check'></i> Edit Simple and Variable Products with Excel</p>						
						<p><i class='fa fa-check'></i> Delete Simple/Variable Products from UI or Excel</p>
						<p><i class='fa fa-check'></i> Import Multiple Child-Parent Category Terms from UI or Excel</p>
						<p><i class='fa fa-check'></i> Delete Category Terms from UI or Excel </p>	
						<p><i class='fa fa-check'></i> Export Products to Excel</p>	
						<p><i class='fa fa-check'></i> Import Featured Image with Excel</p>	
						<p><i class='fa fa-check'></i> Import Product Gallery Images with Excel</p>	
						<p><i class='fa fa-check'></i> Define Product as downloadable and add download URL, name, expiry date with Excel</p>	
					</div>	
					<p class='center' >
						<a class='premium_button' target='_blank'  href='https://extend-wp.com/product/woocommerce-product-excel-importer-bulk-editing-pro/'>
							<i class='fa fa-tag' ></i> <?php _e("Get it here","webd_woocommerce_bulk_edit");?>	
						</a>
						<a href='https://www.youtube.com/watch?v=wWrKy64LIGw&rel=0' target='_blank'>Watch on Youtube</a><br/><br/>
					</p>
		</div>			
	</div>
	

	
	
	<?php
	webd_woocommerce_product_excel_importer_bulk_edit_form_footer();
}

function webd_woocommerce_product_excel_importer_bulk_edit_form_header() {
?>
	<h2><img src='<?php echo plugins_url( 'images/webd_woocommerce_product_excel_importer_bulk_edit.png', __FILE__ ); ?>' style='width:100%' />
<?php
}

function webd_woocommerce_product_excel_importer_bulk_edit_form_footer() {
?>
	<hr>
			<a target='_blank' class='web_logo' href='https://extend-wp.com/'>
			
				<img  src='<?php echo plugins_url( 'images/extendwp.png', __FILE__ ); ?>' alt='Get more plugins by extendWP' title='Get more plugins by extendWP' />
			</a>
			
			<div id='excel_bulk_wrap_free_instructionsVideo' class='rightToLeft'><iframe width="560" height="315" src="https://www.youtube.com/embed/p8PPBUsHA_I?rel=0" frameborder="0" allowfullscreen></iframe>
			</div>			
<?php
}

?>