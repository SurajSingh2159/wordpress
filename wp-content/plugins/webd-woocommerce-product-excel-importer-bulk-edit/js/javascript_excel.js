(function( $ ) {


	$('.excel_bulk_wrap_free #upload').attr('disabled','disabled');
    $(".excel_bulk_wrap_free #file").change(function () {
        var fileExtension = ['xls', 'xlsx'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only format allowed: "+fileExtension.join(', '));	
			$(".excel_bulk_wrap_free input[type='submit']").attr('disabled','disabled');
        }else{
			$('#upload').removeAttr('disabled');
			$(".excel_bulk_wrap_free").find('form').submit();
		}
    });
	
	$(".excel_bulk_wrap_free #product_import").on("submit", function (e) {
		e.preventDefault();
				var data = new FormData();
				$.each($('#file')[0].files, function(i, file) {
					data.append('file', file);
				});	
				data.append('_wpnonce',$("#_wpnonce").val());
				data.append('importProducts',$("#importProducts").val() );
				url= window.location.href;
				$.ajax({
					url: window.location.href,
					data: data,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					beforeSend: function() {	
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$('.excel_bulk_wrap_free').addClass('loading');	
					},					
					success: function(response){
						$(".result").slideDown().html($(response).find(".result").html());
						$('.excel_bulk_wrap_free').removeClass('loading');	
						$("#product_import").fadeOut();	
						dragDrop();	
						
						$(".excel_bulk_wrap_free #product_process").on('submit',function(e) {
							e.preventDefault();
							if($("input[name='post_title']").val() !='' ){
								$.ajax({
									url: $(this).attr('action'),
									data:  $(this).serialize(),
									type: 'POST',
									beforeSend: function() {									
										$("html, body").animate({ scrollTop: 0 }, "slow");
										$('.excel_bulk_wrap_free').addClass('loading');		
									},						
									success: function(response){
										$(".result").slideDown().html($(response).find(".result").html());
										//$("body").html(response);
										$('.excel_bulk_wrap_free').removeClass('loading');	
										$("form")[0].reset();
										$("#product_import").delay(5000).fadeIn();	
									}
								});	
							}else alert('Title Selection is Mandatory.');
						});	
			
					}
				});			
			});	
			//drag and drop
			function dragDrop(){
				$('.excel_bulk_wrap_free .draggable').draggable({cancel:false});
				$( ".excel_bulk_wrap_free .droppable" ).droppable({
				  drop: function( event, ui ) {
					$( this ).addClass( "ui-state-highlight" ).val( $( ".ui-draggable-dragging" ).val() );
					$( this ).attr('value',$( ".ui-draggable-dragging" ).attr('key')); //ADDITION VALUE INSTEAD OF KEY	
					$( this ).attr('placeholder',$( ".ui-draggable-dragging" ).attr('value')); 				
					$( ".ui-draggable-dragging" ).css('visibility','hidden'); //ADDITION + LINE
					$( this ).css('visibility','hidden'); //ADDITION + LINE
					// alert($(this).attr('key'));
					$( this ).parent().css('background','#90EE90');			
				  }		 
				});		
			}
			dragDrop();

})( jQuery )