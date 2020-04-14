<?php

class contentExcelImporterProducts extends contentExcelImporterQuery{

	public $numberOfRows=1;	
	
	public function importProductsDisplay(){?>
	   <h2>IMPORT / UPDATE CONTENT</h2>
	
		<div class='product_content'>	
		
			<p><?php _e("Download the sample excel file, save it and add your products. You can add your Custom Columns. Upload it using the form below.","contentExceIimporter");?> <a href='<?php echo plugins_url( '/example_excel/import_update_products.xlsx', __FILE__ ); ?>'>SAMPLE EXCEL FILE</a></p>		
			<p>	 
		</div>
		<div class='randomContent'>		
			<p><?php _e("Download the sample excel file, save it and add your content. You can add your Custom Columns. Upload it using the form below.","woocommerce_importer");?> <a href='<?php echo plugins_url( '/example_excel/content.xlsx', __FILE__ ); ?>'>CONTENT SAMPPLE FILE</a></p>
		</div>
		
	   <div>			
			
		<?php $this->selectPostTypeForm(); ?>						
										
			<form method="post" id='product_import' class='excel_import' enctype="multipart/form-data" action= "<?php echo admin_url( 'admin.php?page=content-excel-importer-pro&tab=main' ); ?>">
					<table class="form-table">
						<tr valign="top">
						<!--<th scope="row"><?php _e( 'EXCEL FILE', 'contentExceIimporter' ) ?></th>-->
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
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')  && $_POST['importProducts']  ){
			
			check_admin_referer( 'excel_upload' );
			check_ajax_referer( 'excel_upload' );	
			
			$filename=$_FILES["file"]["tmp_name"];
							
			if($_FILES["file"]["size"] > 0 ){
				if($_FILES["file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
					require_once( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');
					try {
						$objPHPExcel = PHPExcel_IOFactory::load($filename);
					} catch(Exception $e) {
						die('Error loading file "'.pathinfo($filename,PATHINFO_BASENAME).'": '.$e->getMessage());
					}
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					 $data = count($allDataInSheet);  // Here get total count of row in that Excel sheet
					 $total =  $data;
					 $totals =  $total-1;
					 //GET ROW NAMES ----- WORKING!!!!!
					$rownumber=1;
					$row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
										
				$titleArray = array();	

					$post_type = $_POST['getPostType'];
				
					print "<span style='display:none' class='thisNum'></span>";
					print "	<div class='ajaxResponse'></div>";		
						
					 print "<div style='overflow:hidden;min-height:400px;width:100%;'>
					 <form method='POST' style='overflow:hidden;min-height:400px;width:100%;' id='product_process' action= ".admin_url( 'admin.php?page=content-excel-importer' ).">";

					 print "<p style='font-style:italic'>DATA MAPPING: Drag and drop excel columns on the right to product properties on the left.</p>";
					 
						print "<div style='float:right;width:50%'>";
							print "<h2 style='color:#0073aa;'>EXCEL COLUMNS</h2><p>";
							foreach ($cellIterator as $cell) {
								//getValue
								echo "<input type='button' class='draggable' style='min-width:200px' key ='".sanitize_text_field($cell->getColumn())."' value='". sanitize_text_field($cell->getValue()) ."' />  <br/>";
							}				
						print "</p></div>";
						print "<div style='float:right;width:50%text-align:right;padding-right:20px'>";
						print "<h2 style='color:#0073aa;'>FIELDS</h2>"; ?>						
						
						<?php print $this->getFields($post_type); ?>
								
						<?php  print "<input type='hidden' name='finalupload' value='".$total."' />
						   <input type='hidden' name='import' value='import' />
						   <input type='hidden' name='start' value='2' />
						   <input type='hidden' name='action' value='import_content' />";?>
						   <?php wp_nonce_field('excel_process','secNonce'); ?><?php
							submit_button('Upload','primary','check');
						print "</div>";				
					print "</form></div>";
					
										
					move_uploaded_file($_FILES["file"]["tmp_name"], plugin_dir_path( __FILE__ ).'import.xlsx');
															
				} else   "<h3>". _e('Invalid File:Please Upload Excel File')."</h3>";	
			}
		}
	
	}

	
}



function import_content(){	
		
		if(isset($_POST['finalupload']) && current_user_can('administrator')){
			$time_start = microtime(true);
			check_admin_referer( 'excel_process','secNonce' );
			check_ajax_referer( 'excel_process' ,'secNonce');
			
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');	
			$filename = plugin_dir_path( __FILE__ ).'import.xlsx';
			
			$objPHPExcel = PHPExcel_IOFactory::load($filename);
			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet	
			$images=array();
			$finalIdsArray=array();
			$finalVarIdsArray=array();
			
			$multiarray= array();
			
			if(!empty($_POST['post_title']) ){
				
				$idsArray = array();
		
					//HERE ALL THE MAGIC HAPPENs
					$i=$_POST['start'];
					$start = $i -1;

					//sleep(2);
						//SANITIZE AND VALIDATE title and description
					if($_POST['post_status'] !=''){
						$status = sanitize_text_field($allDataInSheet[$i][$_POST['post_status']]);
					}else $status='publish';	
						
					$title = sanitize_text_field($allDataInSheet[$i][$_POST['post_title']]);
					
					
					if(!empty($allDataInSheet[$i][$_POST['post_name']])){
						$url = sanitize_title_with_dashes($allDataInSheet[$i][$_POST['post_name']]);
					}
					
										
					$author = sanitize_text_field($allDataInSheet[$i][$_POST['post_author']]);
					$type = $_POST['post_type'];

					$excerpt = wp_specialchars_decode($allDataInSheet[$i][$_POST['post_excerpt']], $quote_style = ENT_QUOTES  );
					$excerpt =  preg_replace('#<script(.*?)>(.*?)</script>#is', '', $excerpt);						
					
					$content = wp_specialchars_decode($allDataInSheet[$i][$_POST['post_content']], $quote_style = ENT_QUOTES  );
					$content =  preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);		

					
					if(empty($allDataInSheet[$i][$_POST['post_date']])){
						$date = current_time('mysql');
					}else{
						$date_string = $allDataInSheet[$i][$_POST['post_date']];
						$date_stamp = strtotime($date_string);
						$date = date("Y-m-d H:i:s", $date_stamp);						
					}
					
						//check if post exists
						if(post_exists($title) == 0){
							$post = array(
								'post_author'   => $author,
								'post_date'   => $date,
								'post_title'   => $title,
								'post_name'   => $url,
								'post_content' => $content,
								'post_status'  => $status,
								'post_excerpt' => $excerpt,
								'post_type'    => $type
							 );									
							
							//create
							$id = wp_insert_post( $post, $wp_error );
							print "<p class='success'><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> created.</p>";
							//print $id ." this is the ID<br/>";
							wp_set_object_terms ($id,'simple','product_type');
							if(in_array($id,$idsArray)){							
							}else array_push($idsArray,$id);
							
						}else{
							//update 
							$id = 	post_exists($title);
							if(in_array($id,$idsArray)){								
							}else array_push($idsArray,$id);							
							if($content !='' ){
							$post = array(
								'ID' 		   => $id,
								'post_author'   => $author,
								'post_date'   => $date,
								'post_title'   => $title,
								'post_name'   => $url,
								'post_content' => $content,
								'post_status'  => $status,
								'post_excerpt' => $excerpt,
								'post_type'    => $type
							 );									
							}else{
							$post = array(
								'ID' 		   => $id,
								'post_author'   => $author,
								'post_date'   => $date,								
								'post_title'   => $title,
								'post_name'   => $url,
								'post_status'  => $status,
								'post_excerpt' => $excerpt,
								'post_type'    => $type
							 );	
							}
							wp_update_post($post, $wp_error );
							print "<p class='warning'><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> already exists. Updated.</p>";
						}
					
					
					if($type =='product'){
							
						
						//SANITIZE AND VALIDATE meta data
						$sale_price = sanitize_text_field($allDataInSheet[$i][$_POST['_sale_price']]);					
						if ( !$sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
						  $sale_price = '';
						  if(!empty($sale_price)){print "For sale price of {$title} you need numbers entered.<br/>";}
						}
						$regular_price = sanitize_text_field($allDataInSheet[$i][$_POST['_regular_price']]);
						if ( !$regular_price  && !empty($allDataInSheet[$i][$_POST['_regular_price']])) {
						  $regular_price = '';
						  if(!empty($regular_price)){print "For regular price of {$title} you need numbers entered.<br/>";}
						}
						$sku = sanitize_text_field($allDataInSheet[$i][$_POST['_sku']]);					
						if ( !$sku && !empty($allDataInSheet[$i][$_POST['_sku']]) ) {
						  $sku = '';
						  //print "For sku of {$title} you need numbers entered.<br/>";
						}
						$weight = sanitize_text_field($allDataInSheet[$i][$_POST['_weight']]);					
						if ( !$weight  && !empty($allDataInSheet[$i][$_POST['_weight']]) ) {
						  $weight = '';
						  if(!empty($weight)){print "For weight of {$title} you need numbers entered.<br/>";}			  
						}	
						$stock = sanitize_text_field($allDataInSheet[$i][$_POST['_stock']]);					
						if ( !$stock  && !empty($allDataInSheet[$i][$_POST['_stock']]) ) {
						  $stock = '';					  
						  if(!empty($stock)){print "For stock of {$title} you need numbers entered.<br/>";}
						}	
						$varDescription = sanitize_text_field($allDataInSheet[$i][$_POST['_variation_description']]);					
						if ( !$varDescription  && !empty($_POST['_variation_description']) ) {
						  $varDescription = '';					  
						}					
						$length = (int)$allDataInSheet[$i][$_POST['_length']];					
						if ( !$length  && !empty($allDataInSheet[$i][$_POST['_length']]) ) {
						  $length = '';
						  if(!empty($weight)){print "For length of {$title} you need numbers entered.<br/>";}			  
						}	
						$width = sanitize_text_field($allDataInSheet[$i][$_POST['_width']]);					
						if ( !$width  && !empty($allDataInSheet[$i][$_POST['_width']]) ) {
						  $width = '';					  
						  if(!empty($width)){print "For width of {$title} you need numbers entered.<br/>";}
						}	
						$height = sanitize_text_field($allDataInSheet[$i][$_POST['_height']]);					
						if ( !$height  && !empty($_POST['_height']) ) {
						  $height = '';					  
						}
						$virtual = sanitize_text_field($allDataInSheet[$i][$_POST['_virtual']]);					
						if ( !$virtual  && !empty($_POST['_virtual']) ) {
						  $virtual = '';					  
						}						
					}	
						$x=0;
						$attributes=array();
						$attrVal = array();
						$concat=array();
						
						
						$taxonomy_objects = get_object_taxonomies( $type, 'objects' );	
						
						if($type =='product'){
									//UPDATE POST META		
									if($sku!='')update_post_meta( $id, '_sku', $sku );
									if($weight!='')update_post_meta( $id, '_weight',$weight );
									if($regular_price!='')update_post_meta( $id, '_regular_price', $regular_price );
									if($sale_price!='')update_post_meta( $id, '_sale_price', $sale_price );
									if($stock!='')update_post_meta( $id, '_stock', $stock );
									update_post_meta( $id, '_visibility', 'visible' );
									if($sale_price!='')update_post_meta( $id, '_price', $sale_price );	
									if( $stock != ''){
										if($stock=='0' ){
											update_post_meta( $id, '_stock_status', 'outofstock');
										}else update_post_meta( $id, '_stock_status', 'instock');
										update_post_meta( $id, '_manage_stock', 'yes');
									}else update_post_meta( $id, '_stock_status', 'instock');
									if($length!='')update_post_meta( $id, '_length', $length );
									if($width!='')update_post_meta( $id, '_width', $width );
									if($height!='')update_post_meta( $id, '_height', $height );
									if($virtual!='')update_post_meta( $id, '_virtual', $virtual );
									wc_delete_product_transients( $id );
						}
						
						foreach( $taxonomy_objects as $voc){				
							//if($voc->name != 'product_type' ){
							if($voc->name == 'product_tag' ||  $voc->name == 'product_cat' || $voc->name =='language' || $voc->name =='post_translations' || $voc->name =='category' || $voc->name =='post_tag' ){
								
														
									$taxToImport =  explode(',',sanitize_text_field($allDataInSheet[$i][$_POST[$voc->name]]));
									foreach($taxToImport as $taxonomy){
										//wp_set_object_terms( $id,$taxonomy,$voc->name,true); //true is critical to append the values
										
										$lang = sanitize_text_field($_POST['language']);
										if($voc->name =='language'){
											if ( class_exists( 'Polylang' ) ) {
												global $polylang;
												$polylang->model->set_post_language($id, $taxonomy);												
											}											
										}
										
										if($voc->name =='post_translations'){
											
											if ( class_exists( 'Polylang' ) ) {
												global $polylang;
												
												$translateId  = post_exists($taxonomy);
												 $translateLang = pll_get_post_language($translateId, 'slug');
												 $translations = $polylang->model->post->get_translations(translateId);
																							
												
												$polylang->model->save_translations($type, $id , array($translateLang => $translateId));											
											}
											
											wp_set_object_terms($id,post_exists($taxonomy), 'post_translations', true);
											//wp_set_object_terms($id,get_page_by_title( $taxonomy,'OBJECT', $type ), 'post_translations', true);										
										}else wp_set_object_terms($id,$taxonomy, $voc->name, true);
										
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
			
				
					if($i == $_REQUEST['finalupload']){
						
						$tota = $_REQUEST['finalupload']-1;
						print "<div class='importMessageSussess'><h2>".$i." / ".$_REQUEST['finalupload']." - JOB DONE! <a href='".admin_url( "edit.php?post_type=".$type )."' target='_blank'><i class='fa fa-eye'></i> GO VIEW YOUR ".$type."s!</a></h2></div>";
					}else{
						print "<div class='importMessage'><h2>".$i." / ".$_REQUEST['finalupload']." Please don't close this page... Your ".$type."s are imported....</h2>
						<p><img  src='".plugins_url( 'images/loading.gif', __FILE__ )."' /></p>
						</div>";
					}
					
					die;							
				
			}else print "<p class='warning' style='color:red' >No title selected for your content.</p>";
			unlink($filename);
			
			$time_end = microtime(true);
			//dividing with 60 will give the execution time in minutes other wise seconds
			$execution_time = ($time_end - $time_start)/60;
			//execution time of the script
			echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
													
		}

					
	}