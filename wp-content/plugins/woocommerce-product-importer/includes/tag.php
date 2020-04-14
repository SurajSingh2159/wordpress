<?php
function woo_pi_generate_tags() {

	global $wpdb, $import;

	@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

	// Check if Tags is empty
	if( !empty( $import->csv_tag ) ) {
		// Check if Tags only contains a single header
		$size = count( $import->csv_tag );
		if( $import->skip_first && $size == 1 ) {
			$import->log .= "<br />" . __( 'No Product Tags were provided', 'woocommerce-product-importer' );
			return;
		}
		if( $import->skip_first )
			$i = 1;
		else
			$i = 0;
		$term_taxonomy = 'product_tag';
		$tags = array();
		for( ; $i < $size; $i++ ) {
			if( isset( $import->csv_tag[$i] ) ) {
				// Check if this cell contains multiple Tags
				if( strpos( $import->csv_tag[$i], $import->category_separator ) ) {
					$tags_explode = explode( $import->category_separator, $import->csv_tag[$i] );
					for( $j = 0; $j < count( $tags_explode ); $j++ ) {
						if( isset( $tags_explode[$j] ) && trim( $tags_explode[$j] ) !== '' )
							$tags[] = trim( $tags_explode[$j] );
					}
					unset( $tags_explode );
				} else {
					if( isset( $import->csv_tag[$i] ) && trim( $import->csv_tag[$i] ) !== '' )
						$tags[] = trim( $import->csv_tag[$i] );
				}
			}
		}
		$size = count( $tags );
		$include_log = true;
		if( !empty( $tags ) ) {
			if( $size > 1000 ) {
				$import->log .= "<br />>>> " . sprintf( __( 'We have just processed and generated so many Product Tags that we couldn\'t actually show you it in real-time, ~%d to be precise', 'woocommerce-product-importer' ), $size );
				$include_log = false;
			}
			// Generate Product Tags if they do not already exist
			foreach( $tags as $tag ) {
				if( $include_log )
					$import->log .= "<br />>>> " . sprintf( __( 'Product Tag: %s', 'woocommerce-product-importer' ), $tag );
				if( !term_exists( $tag, $term_taxonomy ) ) {
					if( WOO_PI_DEBUG !== true )
						$response = wp_insert_term( $tag, $term_taxonomy );
					else
						$response = true;
					if( $include_log ) {
						if( !is_wp_error( $response ) ) {
							$import->log .= "<br />>>>>>> " . __( 'Created Product Tag', 'woocommerce-product-importer' );
						} else {
							if( $import->advanced_log )
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Error creating Product Tag - Error - %s', 'woocommerce-product-importer' ), $response->get_error_message() );
							else
								$import->log .= "<br />>>>>>> " . __( 'Error creating Product Tag', 'woocommerce-product-importer' );
						}
					}
				} else {
					if( $include_log )
						$import->log .= "<br />>>>>>> " . __( 'Duplicate of Product Tag detected', 'woocommerce-product-importer' );
				}
				unset( $response );
			}
			$import->log .= "<br />" . __( 'Product Tags have been generated', 'woocommerce-product-importer' );
		}
	} else {
		$import->log .= "<br />" . __( 'No Product Tags were provided', 'woocommerce-product-importer' );
	}

}

function woo_pi_process_tags() {

	global $import, $product;

	// Product Tag association
	$product->tag_term_id = array();
	if( isset( $product->tag ) ) {
		$term_taxonomy = 'product_tag';
		// Check if this cell contains multiple Tags
		if( strpos( $product->tag, $import->category_separator ) ) {
			$tags = explode( $import->category_separator, $product->tag );
			$size = count( $tags );
			for( $i = 0; $i < $size; $i++ ) {
				if( $tag = get_term_by( 'name', $tags[$i], $term_taxonomy ) )
					$product->tag_term_id[] = $tag->term_id;
				unset( $tag );
			}
			unset( $tags );
		} else {
			if( $tag = get_term_by( 'name', $product->tag, $term_taxonomy ) )
				$product->tag_term_id[] = $tag->term_id;
			unset( $tag );
		}
	}

}
?>