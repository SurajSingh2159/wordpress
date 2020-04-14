<?php

class WebdWoocommerceEProducts{

	public function importProductsDisplay(){?>
	   <h2>IMPORT / UPDATE PRODUCTS</h2>
	   <div>	
			
			<p><?php _e("Download the sample excel file, save it and add your products. You can add your Custom Columns. Upload it using the form below.","woocommerce_importer");?> <a href='<?php echo plugins_url( '/example_excel/import_update_products.xlsx', __FILE__ ); ?>'>SAMPLE EXCEL FILE</a></p>
			<form method="post" id='product_import' enctype="multipart/form-data" action= "<?php echo admin_url( 'admin.php?page=webd-woocommerce-product-excel-importer-bulk-edit' ); ?>">

					<table class="form-table">
						<tr valign="top">
						<td><?php wp_nonce_field('excel_upload'); ?>
						<input type="hidden"   name="importProducts" value="1" />
							<div class="uploader" style="background:url(<?php print plugins_url('images/default.png', __FILE__ );?> ) no-repeat left center;" >
								<img src="" class='userSelected'/>
								<input type="file"  required name="file" id='file'  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
							</div>							
						</td>
						</tr>
					</table>
					<?php submit_button('Upload','primary','upload'); ?>
					</form>	
					<div class='result'><?php  $this->importProducts(); ?></div>
						
			</div>
	<?php			
	}
		
	public function importProducts(){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')  && !$_POST['finalupload'] ){
		
			check_admin_referer( 'excel_upload' );
			check_ajax_referer( 'excel_upload' );	
					
			$filename = $_FILES["file"]["tmp_name"];
			
			if($_FILES["file"]["size"] > 0 ){
			IF($_FILES["file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
				//INCLUDE phpExcel
				include( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');

				try {
					$objPHPExcel = PHPExcel_IOFactory::load($filename);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($filename,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet
					
				$rownumber=1;
				$row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
			
				 print "<div style='overflow:hidden;min-height:400px;width:100%;'>
				 <form method='POST' style='overflow:hidden;min-height:400px;width:100%;' id ='product_process' action= ".admin_url( 'admin.php?page=webd-woocommerce-product-excel-importer-bulk-edit&tab=main' ).">";
				 print "<p style='font-style:italic'>DATA MAPPING: Drag and drop excel columns on the right to product properties on the left.</p>";
					print "<div style='float:right;width:50%'>";
						print "<h2 style='color:#0073aa;'>EXCEL COLUMNS</h2><p>";
						foreach ($cellIterator as $cell) {
							//getValue
							echo "<input type='button' class='draggable' style='min-width:200px' key ='".sanitize_text_field($cell->getColumn())."' value='". sanitize_text_field($cell->getValue()) ."' />  <br/>";
						}				
					print "</p></div>";
					print "<div style='float:right;width:50%text-align:right;padding-right:20px'>";
					print "<h2 style='color:#0073aa;'>PRODUCT FIELDS</h2>";
					echo "<p><b>PRODUCT TYPE </b>
						<select name='producttype' style='border:1px solid red;background:#ccc;' readonly>
							<option value=''>Premium Version Only</option>
							<option value='simple'>Simple</option>
							<option value='variable'>Variable</option>						
						</select>
					</p>";	
					
					echo "<p>AUTHOR ID  <input type='text' name='post_author' required readonly class='droppable' placeholder='Drop here column' /></p>";
					echo "<p>URL  <input type='text' name='post_name' required readonly class='droppable' placeholder='Drop here column' /></p>";	
					
					echo "<p>TITLE  <input type='text' name='post_title' required readonly class='droppable' placeholder='Drop here column' /></p>";
					echo "<p>DESCRIPTION <input type='text' name='post_content' required readonly class='droppable' placeholder='Drop here column'  /></p>";
					echo "<p><b>Short DES. - EXCERPT </b><input type='text' name='post_excerpt' required readonly class='droppable' placeholder='Drop here column'  /></p>";					
					$post_meta=array('_sku','_weight','_regular_price','_sale_price','_stock');
					foreach($post_meta as $meta){
						echo "<p>".strtoupper(str_replace('_',' ',esc_attr($meta) ))." <input type='text' style='min-width:200px' name='".esc_attr($meta)."' required readonly class='droppable' placeholder='Drop here column'  /></p>";
					}
					echo "<p>IMAGE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
					echo "<p>IMAGE GALLERY <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
					echo "<p><b>VIRTUAL </b><input type='text' style='min-width:200px' name='_virtual' required readonly class='droppable' placeholder='Downloadable Product'  /></p>";
					echo "<p>DOWNLOADABLE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				
					print "<h3>CATEGORY AND TAGS</h3>";
					$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );			
					foreach( $taxonomy_objects as $voc){
						//ADDITION : INCLUDE ONLY PRODUCT CATEGORY AND TAGS NOT CUSTOM TAXONOMIES
						if($voc->name == 'product_tag' ||  $voc->name == 'product_cat' ){
							echo "<p>". strtoupper(str_replace('_',' ',esc_attr($voc->name))). " <input type='text' style='min-width:200px' name='".esc_attr($voc->name)."' required readonly class='droppable' placeholder='Drop here column' key /></p>";
						}
					}
					echo "<p>Custom Taxonomy <input type='text' name='custom_tax' style='border:1px solid red;background:#ccc;' readonly  placeholder='Premium Version Only'  /></p>";				   
					print "<input type='hidden' name='finalupload' value='1' />";
					wp_nonce_field('excel_process','secNonce');
					submit_button('Upload','primary','check');
					print "</div>";				
				print "</form></div>";
				
				move_uploaded_file($_FILES["file"]["tmp_name"], plugin_dir_path( __FILE__ ).'import.xlsx');

			} else   "<h3>". _e('Invalid File:Please Upload Excel File')."</h3>";	
			}
		}
		
		if($_POST['finalupload'] && current_user_can('administrator')){
				
			check_admin_referer( 'excel_process','secNonce' );
			check_ajax_referer( 'excel_process' ,'secNonce');				

			$filename = plugin_dir_path( __FILE__ ).'import.xlsx';

			include( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');
			$objPHPExcel = PHPExcel_IOFactory::load($filename);
			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet		

			if(!empty($_POST['post_title'])){
				
				for($i=2;$i<=$data;$i++){
										
					//SANITIZE AND VALIDATE title and description				
					$title = sanitize_text_field($allDataInSheet[$i][$_POST['post_title']]);
					if(!empty($allDataInSheet[$i][$_POST['post_content']])){
						$content = sanitize_text_field($allDataInSheet[$i][$_POST['post_content']]);
					}else $content='';
					
					if(!empty($allDataInSheet[$i][$_POST['post_excerpt']])){
						$excerpt = sanitize_text_field($allDataInSheet[$i][$_POST['post_excerpt']]);
					}else $excerpt='';										

					$author = sanitize_text_field($allDataInSheet[$i][$_POST['post_author']]);

					if(!empty($allDataInSheet[$i][$_POST['post_name']])){
						$url = sanitize_title_with_dashes($allDataInSheet[$i][$_POST['post_name']]);
					}
					
					//check if post exists
						if(post_exists($title)){
							//update 
							$id = post_exists($title);
							$post = array(
								'ID' 		   => $id,
								'post_author'   => $author,
								'post_title'   => $title,
								'post_content' => $content,
								'post_status'  => 'publish',
								'post_excerpt' => $excerpt,
								'post_name'   => $url,
								'post_type'    => 'product'
							 );	
							 
							//ADDITION : NOT UPDATING SOMETHING THAT IS NOT PUBLISH
							if('trash' === get_post_status( $id )){
								print "<p class='warning'>". $title . " is in the trash folder, it won't be updated.</p>";
							}else{
								wp_update_post($post, $wp_error );
								print "<p class='warning'><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> already exists. Updated.</p>";
							}				
						}else{
							$post = array(
								'post_author'   => $author,
								'post_title'   => $title,
								'post_content' => $content,
								'post_status'  => 'publish',
								'post_excerpt' => $excerpt,
								'post_name'    => $url,
								'post_type'    => 'product'
							 );	
							$id = wp_insert_post( $post, $wp_error );						 
							print "<p class='success'><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> created.</p>";
							//create
							
							//wp_set_object_terms ($id,'simple','product_type');
						}

						//IMPORT - UPDATE POST META
						
						//SANITIZE AND VALIDATE meta data
						$sale_price = sanitize_text_field($allDataInSheet[$i][$_POST['_sale_price']]);					
						if ( !$sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
						  $sale_price = '';
						  print "For sale price of {$title} you need numbers entered.<br/>";
						}
						$regular_price = sanitize_text_field($allDataInSheet[$i][$_POST['_regular_price']]);
						if ( !$regular_price  && !empty($allDataInSheet[$i][$_POST['_regular_price']])) {
						  $regular_price = '';
						  print "For regular price of {$title} you need numbers entered.<br/>";
						}
						$sku = sanitize_text_field($allDataInSheet[$i][$_POST['_sku']]);					
						if ( !$sku && !empty($_POST['_sku']) ) {
						  $sku = '';
						  print "For sku of {$title} you need numbers entered.<br/>";
						}
						$weight = sanitize_text_field($allDataInSheet[$i][$_POST['_weight']]);					
						if ( !$weight  && !empty($_POST['_weight']) ) {
						  $weight = '';
						  print "For weight of {$title} you need numbers entered.<br/>";
						}	
						$stock = sanitize_text_field($allDataInSheet[$i][$_POST['_stock']]);					
						if ( !$stock  && !empty($_POST['_stock']) ) {
						  $stock = '';
						  print "For stock of {$title} you need numbers entered.<br/>";
						}

						$virtual = sanitize_text_field($allDataInSheet[$i][$_POST['_virtual']]);					
						if ( !$virtual  && !empty($_POST['_virtual']) ) {
						  $virtual = '';					  
						}
						
						update_post_meta( $id, '_sku', $sku );
						update_post_meta( $id, '_weight',$weight );
						update_post_meta( $id, '_regular_price', $regular_price );
						//ADDITION : IF SALE PRICE IS EMPTY DONT UPDATE META
						if ( $sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
							update_post_meta( $id, '_sale_price', $sale_price );
						}
						update_post_meta( $id, '_stock', $stock );
						update_post_meta( $id, '_visibility', 'visible' );
						//ADDITION : IF SALE PRICE IS EMPTY PRICE WILL BE EQUAL TO REGULAR PRICE
						if ( $sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
							update_post_meta( $id, '_price', $sale_price );
						}else update_post_meta( $id, '_price', $regular_price );
						
						if( !empty($stock) ){
							update_post_meta( $id, '_stock_status', 'instock');
							update_post_meta( $id, '_manage_stock', 'yes');
						}
						
						if($virtual!='')update_post_meta( $id, '_virtual', $virtual );
						
						//TAXONOMIES
						
						$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );			
						foreach( $taxonomy_objects as $voc){
							if($voc->name == 'product_tag' ||  $voc->name == 'product_cat' ){

										$taxToImport =  explode(',',sanitize_text_field($allDataInSheet[$i][$_POST[$voc->name]]));
										foreach($taxToImport as $taxonomy){
											wp_set_object_terms( $id,$taxonomy,$voc->name,true); //true is critical to append the values
											
											// GET ALL ASSIGNED TERMS AND ADD PARENT FOR PRODUCT_CAT TAXONOMY!!! 
											$terms = wp_get_post_terms($id, $voc->name );
											foreach($terms as $term){
												while($term->parent != 0 && !has_term( $term->parent, $voc->name, $post )){
													// move upward until we get to 0 level terms
													wp_set_object_terms($id, array($term->parent), $voc->name, true);								
													$term = get_term($term->parent, $voc->name);
												}
											}										
										}							
							}			
						}// end for each taxonomy																	
				}
			}else print "<p class='warning' >No title selected for your products.</p>";
			unlink($filename);
		}	

	}

}