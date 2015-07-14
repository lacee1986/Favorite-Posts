jQuery(document).ready(function(){

	$(document).on('submit', '#fp_post', function(e){
		e.preventDefault();

		var action = 'removePost'
		if($(this).hasClass('save')){
			action = 'savePost'		
		}
		
		var user_id = $('.fp_user_id').val(),
			post_id = $('.fp_id').val()
			$form 	= $(this),
			$li   	= $(this).parent().parent();
			
		
		var data  = {
			'action'  : action,
			'post_id' : post_id,
			'user_id' : user_id
		};
		jQuery.post(ajax_object.ajax_url, data, function(response) {
			if(action == 'removePost'){
				if(!$('body').hasClass('single')){
					$li.css({'overflow' : 'hidden'}).fadeOut('fast','linear',function(){
						$li.remove();	
					});		
				}
				else{
					$form.attr('class', 'save').find('input[type=submit]').val('Read later').attr('class', 'save_it_later');	
				}
			}
			else{
				$form.attr('class', 'remove').find('input[type=submit]').val('Remove bookmark').attr('class', 'remove_it_now');		
			}
		});		
	});
});