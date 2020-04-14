<ul class="subsubsub">
	<li><a href="#tools"><?php _e( 'Tools', 'woocommerce-product-importer' ); ?></a> |</li>
	<li><a href="#import-modules"><?php _e( 'Import Modules', 'woo_pi' ); ?></a></li>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<div id="poststuff">

	<?php do_action( 'woo_pi_before_tools' ); ?>

	<div id="tools" class="postbox">
		<h3 class="hndle"><?php _e( 'Tools', 'woo_pd' ); ?></h3>
		<div class="inside">
			<p><?php _e( 'Extend your store with other WooCommerce extensions from us.', 'woo_pi' ); ?></p>
			<table class="form-table">

				<tr>
					<td>
						<a href="<?php echo $woo_ce_url; ?>"<?php echo $woo_ce_target; ?>><?php _e( 'Export Products to CSV/XML/Excel 2007 (XLS)', 'woo_pi' ); ?></a>
						<p class="description"><?php _e( 'Export store details changes out of your WooCommerce store and into simple, formatted export files.', 'woo_pi' ); ?></p>
					</td>
				</tr>

				<tr>
					<td>
						<a href="<?php echo $woo_st_url; ?>"<?php echo $woo_st_target; ?>><?php _e( 'Store Toolkit', 'woo_pi' ); ?></a>
						<p class="description"><?php _e( 'Store Toolkit includes a growing set of commonly-used WooCommerce administration tools aimed at web developers and store maintainers.', 'woo_pi' ); ?></p>
					</td>
				</tr>

			</table>
		</div>
	</div>
	<!-- .postbox -->

	<?php do_action( 'woo_pi_after_tools' ); ?>

	<?php do_action( 'woo_pi_before_modules' ); ?>

	<div id="import-modules" class="postbox">
		<h3 class="hndle">
			<?php _e( 'Import Modules', 'woo_pi' ); ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'refresh_module_counts', '_wpnonce' => wp_create_nonce( 'woo_pi_refresh_module_counts' ) ) ) ); ?>" style="float:right;"><?php _e( 'Refresh counts', 'woocommerce-product-importer' ); ?></a>
		</h3>
		<div class="inside">
			<p><?php _e( 'Import and merge Product details from other WooCommerce and WordPress Plugins, simply install and activate one of the below Plugins to enable those additional import options.', 'woo_pi' ); ?></p>
			<ul class="subsubsub">
				<li><a href="<?php echo esc_url( add_query_arg( 'module_status', 'all' ) ); ?>"<?php echo ( empty( $module_status ) ? 'class="current"' : '' ); ?>><?php _e( 'All', 'woocommerce-product-importer' ); ?></a> <span class="count">(<?php echo $modules_all; ?>)</span> |</li>
				<li><a href="<?php echo esc_url( add_query_arg( 'module_status', 'active' ) ); ?>"<?php echo ( $module_status == 'active' ? 'class="current"' : '' ); ?>><?php _e( 'Active', 'woocommerce-product-importer' ); ?></a> <span class="count">(<?php echo $modules_active; ?>)</span> |</li>
				<li><a href="<?php echo esc_url( add_query_arg( 'module_status', 'inactive' ) ); ?>"<?php echo ( $module_status == 'inactive' ? 'class="current"' : '' ); ?>><?php _e( 'Inactive', 'woocommerce-product-importer' ); ?></a> <span class="count">(<?php echo $modules_inactive; ?>)</span></li>
			</ul>
			<!-- .subsubsub -->
			<br class="clear" />
			<hr />

<?php if( $modules ) { ?>
			<div class="table table_content">
				<table class="woo_vm_version_table">
	<?php foreach( $modules as $module ) { ?>
					<tr>
						<td class="import_module">
		<?php if( $module['description'] ) { ?>
							<a href="<?php echo add_query_arg( 'ref', 'visserlabs', $module['url'] ); ?>" target="_blank"><strong><?php echo $module['title']; ?></strong></a>: <span class="description"><?php echo $module['description']; ?></span>
		<?php } else { ?>
							<a href="<?php echo add_query_arg( 'ref', 'visserlabs', $module['url'] ); ?>" target="_blank"><strong><?php echo $module['title']; ?></strong></a>
		<?php } ?>
						</td>
						<td class="status">
							<div class="<?php woo_pi_modules_status_class( $module['status'] ); ?>">
		<?php if( $module['status'] == 'active' ) { ?>
								<div class="dashicons dashicons-yes" style="color:#008000;"></div><?php woo_pi_modules_status_label( $module['status'] ); ?>
		<?php } else { ?>
			<?php if( $module['url'] ) { ?>
								<?php if( isset( $module['slug'] ) ) { echo '<div class="dashicons dashicons-download" style="color:#0074a2;"></div>'; } else { echo '<div class="dashicons dashicons-admin-links"></div>'; } ?>&nbsp;<a href="<?php echo $module['url']; ?>" target="_blank"<?php if( isset( $module['slug'] ) ) { echo ' title="' . __( 'Install via WordPress Plugin Directory', 'woo_pi' ) . '"'; } else { echo ' title="' . __( 'Visit the Plugin website', 'woo_pi' ) . '"'; } ?>><?php woo_pi_modules_status_label( $module['status'] ); ?></a>
			<?php } ?>
		<?php } ?>
							</div>
						</td>
					</tr>
	<?php } ?>
				</table>
			</div>
			<!-- .table -->
<?php } else { ?>
			<p><?php _e( 'No import modules are available at this time.', 'woo_pi' ); ?></p>
<?php } ?>
		</div>
		<!-- .inside -->
	</div>
	<!-- .postbox -->

	<?php do_action( 'woo_pi_after_modules' ); ?>

</div>
<!-- #poststuff -->