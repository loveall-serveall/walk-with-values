<?php
//Set Your Nonce
$ajax_nonce = wp_create_nonce( "my-general-string" );
?>


<script>
        //save button onclick function for post value through ajax in option table
        function datasave_general_pagesetting()
        {
            var status= jQuery('input[type="radio"]:checked').val();
		    var background = jQuery('#upload_background').val();
		    var title = jQuery('#title').val();
		    var descrip = jQuery('#descrip').val();
			var google_code = jQuery('#google_code').val();
		    jQuery('#loading_general').show();
			
			
		    if(jQuery('#fbchk').is(':checked'))
		    {
			   
				var fb = jQuery('#fb').val();
				
		    }
		    if(jQuery('#twitchk').is(':checked'))
		    {
                
				 var twit= jQuery('#twit').val();
				
		    }
		    if(jQuery('#gpchk').is(':checked'))
		    {
          
				 var gp= jQuery('#gp').val();
				
		    }
	
            jQuery.ajax(
            {
	    	    type: "POST",
		        url: ajaxurl,
	
		        data : {
			    'action':'coming_soon_page_settings',
			    'security' : '<?php echo $ajax_nonce; ?>',
			    'status':status,
			    'background':background,
			    'title':title,
			    'descrip':descrip,
				'google_code':google_code,
			    'fb':fb,
			    'twit':twit,
			    'gp':gp,
			        },
                success : function(data){
									jQuery('#loading_general').hide();
                                   jQuery('#success_message_save_general').show();
								   jQuery("#success_message_save_general").fadeOut(5000);
								   
								   location.href='?page=coming_soon';
                                   }			
            });
        }

        //reset button onclick function for post value through ajax in option table
        function reset_data_general_pagesetting()
        {
            jQuery.ajax(
            {
		        type: "POST",
		        url: ajaxurl,
	
		        data : {
			        'action':'coming_soon_page_reset_settings',
			         'security' : '<?php echo $ajax_nonce; ?>',
			           },
                success : function(data){
                                   jQuery('#success_message_reset_general').show();
								   jQuery("#success_message_reset_general").fadeOut(5000);
								   location.href='?page=coming_soon';
                                   }
					
            });
	  
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

	background-color: #2f96b4;
background-image: -moz-linear-gradient(top, #52b2cf, #2f96b4);
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#52b2cf), to(#2f96b4));
background-image: -webkit-linear-gradient(top, #52b2cf, #2f96b4);
background-image: -o-linear-gradient(top, #52b2cf, #2f96b4);
background-image: linear-gradient(to bottom, #52b2cf, #2f96b4);
color:#fff;
	box-shadow-color: #21759B;
	font-size:15px;
	border-radius:10px;
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
	box-shadow: 0 10px 20px -1px rgba(0,0,0,0.5);
	
	
}
</style>

        <!--get values from option table -->
        <?php $value=get_option('soon_page_settings'); 
		?>
        <!-- div start general settings get on tabs -->
        <div class="block ui-tabs-panel ui-widget-content ui-corner-bottom" id="option_general" aria-labelledby="ui-id-1" >
            <!--div start for display "status" field  -->
            <div class="option option-input">
               
                 <h3><?php _e('Status');?><span class="icon help" style="float:right">
                  <span class="tooltip">Enable coming soon page from here.</span>
			      </span></h3>
                <div class="section">
                    <div class="element">
                        <input type="radio" name="status" value="0" id="status"  <?php if($value['status']=='0') echo 'checked' ?>/>&nbsp;Disabled<br>
                        <input type="radio" name="status" value="1" id="status" <?php if($value['status']=='1') echo 'checked' ?>/>&nbsp;Coming soon enabled
                    </div>
                </div>
            </div>
            <!-- div end for display "status" field -->
            <!--div start for display "background" field -->
            <div class="option option-upload">
                <h3><?php _e('Background Image');?><span class="icon help" style="float:right">
                <span class="tooltip">The placeholder image that will be used as a background image.</span>
			    </span></h3>
                <div class="section">
	                <div class="element">
		                <input type="text" value="<?php  echo esc_attr($value['background']);  ?>" id="upload_background" name="upload_image" size="36" />
                        <input type="button"  value="Upload Background Image" class="button-primary" style="margin-left:10px;height:30px" />
						</div>
						
						<br><div style="display:block;float:left;"><img style="" src="<?php  echo esc_attr($value['background']);  ?>" id="ecs_backg_img" class="preview_image" ></div>
						
                </div>
            </div>
            <!-- div end for display "background" field -->
            <!-- div start for display "title" field -->
            <div class="option option-input">
                <h3><?php _e('Title');?><span class="icon help" style="float:right">
                  <span class="tooltip">This is the page title and will be displayed on top of the page.</span>
			      </span></h3>
                <div class="section">
                    <div class="element">
                        <input type="text" value="<?php if($value['title']==""){echo "coming soon page";} else{echo $value['title'];} ?>" id="title" name="title" class="" />
                    </div>
                </div>
            </div>
            <!-- div end for display "title" field -->
            <!-- div start for display "description" field -->
            <div class="option option-input">
                <h3><?php _e('Description');?><span class="icon help" style="float:right">
                <span class="tooltip">This will be displayed below the Page Title.</span>
			    </span></h3>
                <div class="section">
                    <div class="element">
                        <textarea name="descrip" id="descrip" rows="10" cols="100"><?php if($value['descrip']==""){echo "This website will launch soon. To connect with us enter your email below.";} else{echo $value['descrip'];} ?> </textarea>
                    </div>
                </div>
            </div>
            <!-- div end for display "description" field -->
           <!-- div start for display "description" field -->
            <div class="option option-input">
                <h3><?php _e('Google Analytics Code');?><span class="icon help" style="float:right">
                <span class="tooltip">Insert the tracking code here. For example Google Analytics code can be inserted here.</span>
			    </span></h3>
                <div class="section">
                    <div class="element">
                        <textarea name="google_code" id="google_code" rows="4" cols="100"><?php echo $value['google_code']; ?> </textarea>
                    </div>
                </div>
            </div>
            <!-- div end for display "description" field -->
          
            <!-- div start for display "Social media links" field -->
            <div class="option option-input">
                <h3><?php _e('Social Media Links');?><span class="icon help" style="float:right">
                <span class="tooltip">Make you page Social. Insert social links here.</span>
			    </span></h3>
                <div class="section">
                    <div class="element">
                        <div>
                            <!-- facebook field with checkbox -->
                            <img src="<?php echo plugins_url('images/facebook.png',__FILE__) ?>" align="left" />
                            <input type="text" name="fb" id="fb" value="<?php echo $value['fb']; ?>" placeholder="Enter facbook url" style="width:250px; margin-left:10px" />
                            <input type="checkbox" name="fbchk" id="fbchk" style="margin-top:5px; margin-left:6px;" <?php if($value['fb']!='') echo 'checked'?>/><br /><br />
                            <!-- twitter field with checkbox -->
                            <img src="<?php echo plugins_url('images/twitter.png',__FILE__) ?>" align="left" />
                            <input type="text" name="twit" id="twit" value="<?php echo $value['twit']; ?>" placeholder="Enter twitter url" style="width:250px; margin-left:10px" />
                            <input type="checkbox" name="twitchk" id="twitchk" style="margin-top:5px; margin-left:6px;" <?php if($value['twit']!='') echo 'checked'?>/><br /><br />
                            <!-- google plus field with checkbox -->
                            <img src="<?php echo plugins_url('images/googleplus.png',__FILE__) ?>" align="left" />
                            <input type="text" name="gp" id="gp" value="<?php echo $value['gp']; ?>" placeholder="Enter google-plus url" style="width:250px; margin-left:10px" />
                            <input type="checkbox" name="gpchk" id="gpchk" style="margin-top:5px; margin-left:6px;" <?php if($value['gp']!='') echo 'checked'?>/>
                        </div>
                    </div>
                </div>
            </div>
            <!-- div end for display "Social media links" field -->
             <!-- save and reset button -->
             <input type="button" class="button-framework save-options"  value= "<?php _e('Save Changes');?>" onclick="datasave_general_pagesetting()"/>
             <input type="button" class="button-framework reset"  value="<?php _e('Restore Defaults');?>" onclick="reset_data_general_pagesetting()" />
			 &nbsp;<img id="loading_general" style="display:none;
vertical-align:middle;" class="loading" src="<?php echo plugins_url('/images/loading.gif',__FILE__);?>" />
			 <div id="success_message_reset_general" ><?php _e('Data reset sucessfully');?> </div>
		     <div id="success_message_save_general" ><?php _e('Data save sucessfully');?></div>
        </div>
        <!-- general setting page final div is closed -->