<?php


class contentExcelImporterQuery{
	


	public function selectPostType(){ ?>
		<select  name="contentExcelImporter_post_type" id="contentExcelImporter_post_type"   value="" >
		<option value=''>Select</option>
		<?php if($_REQUEST['contentExcelImporter_post_type']){
			?><option value='<?php print sanitize_text_field($_REQUEST['contentExcelImporterPro_post_type']); ?>'><?php print sanitize_text_field($_REQUEST['contentExcelImporter_post_type']); ?></option><?php
		}
		
		$postType = array('post','page','product');
		
		foreach (  get_post_types( '', 'names' ) as $post_type ) {
			if(in_array($post_type,$postType) ){
				echo "<option value='".esc_attr($post_type)."'>" . $post_type . "</option>";
			}
			
		}	
		?></select>	 <?php 	
	}
	
	public function selectPostTypeForm(){
		
		if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='main' || empty($_REQUEST['tab']) ){
			print "<form id='selectPostType' action= '".admin_url( 'admin.php?page=content-excel-importer' )."' method='POST'>";
		}else print "<form id='selectPostType' action= '".admin_url( 'admin.php?page=content-excel-importer-pro' )."' method='POST'>"; 		
		?>
		
		<label>SELECT POST TYPE</label>

		<?php $this->selectPostType(); ?>
		
		<input type='hidden' name='getPostType' value='1'  />
		<?php wp_nonce_field('getPostType','getPostType'); ?>
		
		</form>
		<?php	
	}

	public function getFields($post_type){
		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')  ){
			
			print "<p><b>POST TYPE </b> <input type='text' name='post_type' id='post_type_insert' required readonly  value='".$post_type."' /></p>";
			
				
				
			
			$data =array('post_title','post_author','post_date','post_name','post_status','post_content','post_excerpt','image','_virtual');
			
			$pro = array('image_gallery','_downloadable','download_name','download_file','_download_limit','_download_expiry');
			
			foreach($data as $d){
				if($d=='post_name'){
					print "<p><b>url </b> <input type='text' name='".$d."'  readonly class='droppable' placeholder='Drop here column' /></p>";
				}elseif($d=='image'){
					echo "<p class='proVersion' >IMAGE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='PRO Version Only'  /></p>";					
				}else  print "<p><b>".esc_attr($d)." </b> <input type='text' name='".$d."'  readonly class='droppable' placeholder='Drop here column' /></p>";
			}
			if($post_type=='product'){
				foreach($pro as $d){
					print "<p  class='proVersion' ><b>".esc_attr($d)." </b> <input  style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly placeholder='PRO Version Only' /></p>";
				}
			}
			
			if($post_type=='post'){
				$taxonomy_objects = get_object_taxonomies( 'post', 'objects' );			
				foreach( $taxonomy_objects as $voc){
					//ADDITION : INCLUDE ONLY PRODUCT CATEGORY AND TAGS NOT CUSTOM TAXONOMIES
					if($voc->name == 'post_tag' ||  $voc->name == 'category' || $voc->name =='language' || $voc->name =='post_translations' ){
						echo "<p>". strtoupper(str_replace('_',' ',esc_attr($voc->name))). " <input type='text' style='min-width:200px' name='".esc_attr($voc->name)."' required readonly class='droppable' placeholder='Drop here column' key /></p>";
					}
				}
			}
			if($post_type=='page'){
				$taxonomy_objects = get_object_taxonomies( 'page', 'objects' );			
				foreach( $taxonomy_objects as $voc){
					//ADDITION : INCLUDE ONLY PRODUCT CATEGORY AND TAGS NOT CUSTOM TAXONOMIES
					if($voc->name =='language' || $voc->name =='post_translations' ){
						echo "<p>". strtoupper(str_replace('_',' ',esc_attr($voc->name))). " <input type='text' style='min-width:200px' name='".esc_attr($voc->name)."' required readonly class='droppable' placeholder='Drop here column' key /></p>";
					}
				}
			}
			
			if($post_type=='product'){
				
				$post_meta=array('_sku','_weight','_regular_price','_sale_price','_stock');
				foreach($post_meta as $meta){
					echo "<p>".strtoupper(str_replace('_',' ',esc_attr($meta) ))." <input type='text' style='min-width:200px' name='".esc_attr($meta)."' required readonly class='droppable' placeholder='Drop here column'  /></p>";
				}
				
				print "<h3>CATEGORY AND TAGS</h3>";
				$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );			
				foreach( $taxonomy_objects as $voc){
					//ADDITION : INCLUDE ONLY PRODUCT CATEGORY AND TAGS NOT CUSTOM TAXONOMIES
					if($voc->name == 'product_tag' ||  $voc->name == 'product_cat' ){
						echo "<p>". strtoupper(str_replace('_',' ',esc_attr($voc->name))). " <input type='text' style='min-width:200px' name='".esc_attr($voc->name)."' required readonly class='droppable' placeholder='Drop here column' key /></p>";
					}
				}				
			}
			
			echo "<p>Custom Taxonomy <input type='text' name='custom_tax' style='border:1px solid red;background:#ccc;' readonly  placeholder='Premium Version Only'  /></p>";				
			
		}
	}	
	

	
}
