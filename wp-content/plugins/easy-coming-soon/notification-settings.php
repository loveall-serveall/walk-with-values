<?php
//Set Your Nonce
$ajax_nonce = wp_create_nonce( "my-notification-string" );
?>

<script>

function datasave_footer()
{

	 var mailto = jQuery( "#mailingarea" ).val();
			var email_address = jQuery('#email').val();
			
			var message =  jQuery('#message').val();
			
		var sb_btn = jQuery('#sb_btn').val();
		var placeholder_text = jQuery('#placeholder_text').val();
			
			
			if(mailto == 'yes')
			{
			if(email_address == '')
			{
				alert("Please enter your email");
				return false;
			}
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!regex.test(email_address))
			{
				alert("Please Enter a valid Email Address");
				return false;
			}			
			
			}
			
			jQuery('#loading_notify').show();
            jQuery.ajax(
            {
	    	    type: "POST",
		        url: ajaxurl,
	
		        data : {
			    'action':'coming_soon_page_notification_settings',
			    'security' : '<?php echo $ajax_nonce; ?>',
			    
				'mailto':mailto,
				'email_address':email_address,
				'message':message,
				'sb_btn':sb_btn,
				'placeholder_text':placeholder_text,
			    
			        },
                success : function(data){
									jQuery('#loading_notify').hide();
                                   jQuery('#success_message_save_footer').show();
								   jQuery("#success_message_save_footer").fadeOut(5000);
								   //location.href='?page=coming_soon';
                                   }			
            });

	  

}

function reset_data_footersetting()
{
 jQuery.ajax(
            {
		        type: "POST",
		        url: ajaxurl,
	
		        data : {
			        'action':'coming_soon_page_reset_notification_settings',
			        'security' : '<?php echo $ajax_nonce; ?>',
			           },
                success : function(data){
                                   jQuery('#success_message_reset_footer').show();
								   jQuery("#success_message_reset_footer").fadeOut(5000);
								   location.href='?page=coming_soon';
                                   }
					
            });
			

}
	
	function mail_field_display()
{
	var mailfield = jQuery("#mailingarea").val();
	//alert(mailfield)
	if(mailfield=="no"){ hideAll();	}
	if(mailfield=="yes"){ show_mail_field(); }
	
}

function hideAll()
{
	jQuery('#mail_field').hide();
	jQuery('#message_field').hide();
	jQuery('#subscribe_button').hide();
	jQuery('#placeholder').hide();
	
}

function show_mail_field()
{
	jQuery('#mail_field').show();
	jQuery('#message_field').show();
	jQuery('#subscribe_button').show();
	jQuery('#placeholder').show();
	
}
		
		
</script>
<style>
/* =Tooltips style
========================================================================*/

.icon {
	display: inline-block;
	width: 16px;
	height: 16px;
	position: relative;
	padding: 0 4px 0 0;
	
	background: url(<?php echo plugins_url('images/icons.png',__FILE__) ?>) no-repeat;
}

.tooltip {
	display: none;
	width: 200px;
	position: absolute;
	padding: 10px;
	margin: 4px 0 0 4px;
	top: 0;
	right: 16px;
	border: 1px solid #76B6D7;
	border-radius: 0 8px 8px 8px;
	background: #bedffe;
	font-size: 13px;
	box-shadow: 0 1px 2px -1px #21759B;
	z-index: 999;
}

/* Icons Sprite Position */

.help {
	background-position: 0 0;
}

.warning {
	background-position: -20px 0;
}

.error {
	background-position: -40px 0;
}

/* Tooltip Colors */

.help .tooltip {
	border-color: #76B6D7;
	background-color: #bedffe;
	box-shadow-color: #21759B;
}

.warning .tooltip {
	border-color: #cca863;
	background-color: #ffff70;
	box-shadow-color: #ac8c4e;
}

.error .tooltip {
	border-color: #b50d0d;
	background-color: #e44d4e;
	box-shadow-color: #810606;
}

.icon:hover .tooltip {
	display: block;
}
</style>
    <?php  $value=get_option('soon_page_notification_settings');?>
    <div class="block ui-tabs-panel ui-widget-content ui-corner-bottom" id="option_home" aria-labelledby="ui-id-5" role="tabpanel" style="display: none;" aria-expanded="false" aria-hidden="true">

           <!-- div start for display "Enter mail field" field -->
            <div class="option option-input">
                <h3><?php _e('Mailing Option');?><span class="icon help" style="float:right">
                <span class="tooltip">Choose if you want to display a signup / subscribe field on the page.</span></span></h3>
                <div class="section">
                    <div class="element">
                        <select name="mailingarea" id="mailingarea" onchange="mail_field_display()" >
                            <option onclick="hideAll()" value="no" <?php if($value['mailto']=='no') echo 'selected' ?> ><?php _e('Do not display signup field'); ?></option>
                            <option onclick="show_mail_field()" value="yes" <?php if($value['mailto']=='yes') echo 'selected' ?>><?php _e('Mail to the Owner'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- div end for display "Enter mail field" field -->
            <!-- div start for display "Owner email address" field -->
            <div class="option option-input" id="mail_field" style="display:<?php if($value['mailto']=='no') {echo 'none';} elseif($value['mailto']=='') echo 'none' ?>;">
                <h3 style="color:#21759b"><?php _e('Owner Email Address');?><span class="icon help" style="float:right">
                <span class="tooltip">Information of new susbscribers will be sent to this email address.</span></span></h3>
                <div class="section">
                    <div class="element">
                          <input type="text" value="<?php echo $value['email_address'] ?>" id="email" name="email" placeholder="example@gmail.com" class="" />
                    </div>
                </div>
            </div>
            <!-- div end for display "Owner email address" field -->
            <!-- div start for display "Display message to subscriber" field -->
            <div class="option option-input" id="message_field" style="display:<?php if($value['mailto']=='no') {echo 'none';} elseif($value['mailto']=='') echo 'none' ?>;">
                <h3 style="color:#21759b"><?php _e('Display Message after user subscribing');?><span class="icon help" style="float:right">
                  <span class="tooltip">Enter message to display after a user enters his email address.</span></h3>
                <div class="section">
                    <div class="element">
                          <input type="text" value="<?php echo $value['message'] ?>" id="message" name="message" placeholder="Thanks you for subscribing" class="" />
                    </div>
                </div>
            </div>
            <!-- div end for display "message display to subscriber" -->
            <!-- div start for enter "Notify button label" field -->
             <div class="option option-input" id="subscribe_button" style="display:<?php if($value['mailto']=='no') {echo 'none';} elseif($value['mailto']=='') echo 'none' ?>;">
                <h3 style="color:#21759b"><?php _e('Subscribe Button');?><span class="icon help" style="float:right">
                  <span class="tooltip">Customize the button text.</span></h3>
                <div class="section">
                    <div class="element">
                          <input type="text" value="<?php echo $value['sb_btn'] ?>" id="sb_btn" name="sb_btn" placeholder="Notify Me!" class="" />
                    </div>
                </div>
             </div>
            <!-- div end for enter "notify buttton label" field -->
             <!-- div start for enter "email placeholder" field -->
             <div class="option option-input" id="placeholder" style="display:<?php if($value['mailto']=='no') {echo 'none';} elseif($value['mailto']=='') echo 'none' ?>;">
                <h3 style="color:#21759b"><?php _e('Email Textbox Placeholder');?><span class="icon help" style="float:right">
                  <span class="tooltip">Customize the placeholder text of your subscription field</span></h3>
                <div class="section">
                    <div class="element">
                          <input type="text" value="<?php echo $value['placeholder_text'] ?>" id="placeholder_text" name="placeholder_text" placeholder="Enter your email to notify" class="" />
                    </div>
                </div>
             </div>
            <!-- div end for enter "email placeholder" field -->


            <input type="hidden" value="1" id="spa_footer_customization" name="spa_footer_customization" />
            <input type="button" class="button-framework save-options"  value= "<?php _e('Save Changes');?>" onclick="datasave_footer()">

            <input type="button" class="button-framework reset" value="<?php _e('Restore Defaults');?>" onclick="reset_data_footersetting()" />
			&nbsp;<img id="loading_notify" style="display:none;
vertical-align:middle;" class="loading" src="<?php echo plugins_url('/images/loading.gif',__FILE__);?>" />
            <div id="success_message_reset_footer" >
                <?php _e('Data reset sucessfully');?>
            </div>

            <div id="success_message_save_footer" >
                <?php _e('Data save sucessfully');?>
            </div>
    </div>
<style>
#success_message_save_footer{
  display: none;
   margin: 15px 8px 0px 1px;
   padding: 13px 0px 15px 52px;
   background: url(<?php echo plugins_url("/images/icon_check.png",__FILE__) ?>) left no-repeat #21759B;
   /*opacity:0.5;
   filter:alpha(opacity=50);*/
   background-position: 15px 15px;
   border: solid 1px #F22853;
   -webkit-border-radius: 15px;
   -moz-border-radius: 15px;
   border-radius: 15px;
   width: 220px;
   font-size: 20px;
   color: #ffffff;
   position: absolute;
   left: 500px;
   bottom: 20px;
   
}		
#success_message_reset_footer {
  display: none;
   margin: 15px 8px 0px 1px;
   padding: 13px 0px 15px 52px;
    background: url(<?php echo plugins_url("/images/icon_check.png",__FILE__) ?>) left no-repeat #21759B;

   background-position: 15px 15px;
   border: solid 1px #F22853;
   -webkit-border-radius: 15px;
   -moz-border-radius: 15px;
   border-radius: 15px;
   width: 220px;
   font-size: 20px;
   color: #ffffff;
   position: absolute;
   left: 500px;
   bottom: 20px;
}
</style>