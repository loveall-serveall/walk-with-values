
<?php
//Set Your Nonce
$ajax_nonce = wp_create_nonce( "my-design-string" );
?>

<script>
        //data save function call on save button onclick and post by action to option table
        function datasave_design_settings()
        {
            var background_color = jQuery('#backgound_color').val();
		    var title_color = jQuery('#title_color').val();
		    var descrip_color = jQuery('#descrip_color').val();
		    var pxselect_post_title = jQuery('#pxselect_post_title').val();
			var font_family_post_title = jQuery('#font_family_post_title').val();
			var pxselect_post_entry = jQuery('#pxselect_post_entry').val();
		    var font_family_post_description = jQuery('#font_family_post_description').val();
			 var background_effect = jQuery('input:radio[name="cs_noise_effect"]:checked').val();
			 jQuery('#loading_design').show();
	
            jQuery.ajax(
            {
		        type: "POST",
		        url: ajaxurl,
	
		        data : {
			    'action':'coming_soon_page_design_settings',
			    'security' : '<?php echo $ajax_nonce; ?>',
			    'background_color':background_color,
			    'title_color':title_color,
			    'descrip_color':descrip_color,
			    'pxselect_post_title':pxselect_post_title,
			    'font_family_post_title':font_family_post_title,
			    'pxselect_post_entry':pxselect_post_entry,
			    'font_family_post_description':font_family_post_description,
				'background_effect':background_effect,
			        },
                success : function(data){
									 jQuery('#loading_design').hide();
                                   jQuery('#success_message_save').show();
								   jQuery("#success_message_save").fadeOut(5000);
								   //location.href='?page=coming_soon';
                                   }			
            });
        }
        //data reset function is call on reset button onclick and data post by ajax action in option table
        function reset_data_typography()
        {
            jQuery.ajax(
            {
		        type: "POST",
		        url: ajaxurl,
	
		        data : {
			    'action':'coming_soon_page_design_reset_settings',
			    'security' : '<?php echo $ajax_nonce; ?>',
		            },
                success : function(data){
                                   jQuery('#success_message').show();
								   jQuery("#success_message").fadeOut(5000);
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
.wp-color-result {
vertical-align: middle;
}
</style>



        <!-- first div start for diplay design setting on second tab "design" -->
        <div class="block ui-tabs-panel ui-widget-content ui-corner-bottom" id="option_typography" aria-labelledby="ui-id-2" role="tabpanel" style="display: none;">
            <?php $value=get_option('soon_page_desgin_settings');
			$background_effect = $value['background_effect'];
			?>
            <!--code for Menu Style-->
            <!-- div start to diplay page top head title -->
            <div class="option option-checbox">
               
                <h3><?php _e('Background Color');?><span class="icon help" style="float:right">
                <span class="tooltip">Select background color.</span></h3>
            </div>
            <!-- div end to diplay page top head title -->
            <!-- div start to diplay "baackground color" field-->
            <div class="option option-background-upload">
                <div class="section">
                    <div class="element">
                        <div class="form-item" >
						
                            <input  type="text"  class="my-color-field" data-default-color="#ffffff" id="backgound_color"  value="<?php if($value['background_color']=='') { echo '#21759B' ;} else{ echo $value['background_color'];} ?>" />
                        </div>
                       
                    </div>
                </div>
	        </div>
            <!-- div end to diplay "baackground color" field -->

            <!--div start to display "title and fonts" -->
            <div class="option option-checbox">
                <h3><?php _e('"Headline" Font & Color','sis_spa');?><span class="icon help" style="float:right">
                  <span class="tooltip">Customize the font , size and color of the title.</span></h3>
            </div>
            <!--div end to display "title and fonts" -->
            <!--div start to display "title and fonts colors and formats" -->
            <div class="option option-background-upload">
                <div class="section">
                    <div class="element">
						
						<h4 style="font-size:15px;">
						Headline font-color
						</h4>
						
					 <div class="form-item" style="margin-bottom:7px">
		                    <input type="text"   class="my-color-field" data-default-color="#ffffff"  id="title_color" name="color_post_title" value="<?php  $value=get_option('soon_page_desgin_settings'); if($value['title_color']=='') { echo '#ffffff' ;} else{ echo $value['title_color'];} ?>"/>
		                </div>
		               
						<br>
						<div  style="width:20%;display:inline" >
						<h4 style="font-size:15px;">
						Headline font-size
						</h4>
						</div>
             	        <div class="select_wrapper background-attachment" style="width:40%">
                            <select name="post_title_px" id="pxselect_post_title" class="select">
								<?php 
								for ($i = 10; $i <=100; $i++) { 
								?>
                                <option value="<?php echo $i; ?>" <?php $value=get_option('soon_page_desgin_settings'); if ( $value['title_font_size'] == $i ) echo selected($value['title_font_size'], $i ); ?> name=""><?php echo $i; ?></option>
							   <?php } ?>
							</select>
					    </div>
						
			            <div class="select_wrapper background-attachment" style="width:20%;margin-left:10px;margin-bottom:7px" >
							<select id="main_post_title" onchange="return change_post_title()" class="select">
                                <option value="px"><?php _e('px');?></option>
                                <!-- <option value="em">em</option>-->
                            </select>	
				        </div>
						<br>
						<div  style="width:20%;display:inline" >
						<h4 style="font-size:15px;">
						Headline font-style
						</h4>
						</div>
                        <?php $value=get_option('soon_page_desgin_settings'); $font_family_post_title = $value['title_font_format']; ?>
					    <div class="select_wrapper background-attachment" style="width:60%" >
                            <select  name="font_family_post_title" class="select" id="font_family_post_title">
								<optgroup label="Select Fonts">
                                <option value="roboto" <?php selected($font_family_post_title, 'roboto' ); ?>>roboto</option>
                                <option value="Arial, sans-serif" <?php selected($font_family_post_title, 'Arial, sans-serif' ); ?>>Arial</option>
                                <option  value="Verdana, Geneva, sans-serif" <?php selected($font_family_post_title, 'Verdana, Geneva, sans-serif' ); ?>>Verdana</option><option value="Trebuchet MS, Tahoma, sans-serif" <?php selected($font_family_post_title, 'Trebuchet MS, Tahoma, sans-serif' ); ?>>Trebuchet</option><option value="Georgia, serif" <?php selected($font_family_post_title, 'Georgia, serif' ); ?>>Georgia</option><option value="Times New Roman, serif" <?php selected($font_family_post_title, 'Times New Roman, serif' ); ?>>Times New Roman</option><option value="Tahoma, Geneva, Verdana, sans-serif" <?php selected($font_family_post_title, 'Tahoma, Geneva, Verdana, sans-serif' ); ?>>Tahoma</option><option value="Palatino, Palatino Linotype, serif" <?php selected($font_family_post_title, 'Palatino, Palatino Linotype, serif' ); ?>>Palatino</option><option value="Helvetica Neue, Helvetica, sans-serif" <?php selected($font_family_post_title, 'Helvetica Neue, Helvetica, sans-serif' ); ?>>Helvetica*</option><option value="Calibri, Candara, Segoe, Optima, sans-serif" <?php selected($font_family_post_title, 'Calibri, Candara, Segoe, Optima, sans-serif' ); ?>>Calibri*</option><option value="Myriad Pro, Myriad, sans-serif" <?php selected($font_family_post_title, 'Myriad Pro, Myriad, sans-serif' ); ?>>Myriad Pro*</option><option value="Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif" <?php selected($font_family_post_title, 'Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif' ); ?>>Lucida</option><option value="Arial Black, sans-serif" <?php selected($font_family_post_title, 'Arial Black, sans-serif' ); ?>>Arial Black</option><option value="Gill Sans, Gill Sans MT, Calibri, sans-serif" <?php selected($font_family_post_title, 'Gill Sans, Gill Sans MT, Calibri, sans-serif' ); ?>>Gill Sans*</option><option value="Geneva, Tahoma, Verdana, sans-serif" <?php selected($font_family_post_title, 'Geneva, Tahoma, Verdana, sans-serif' ); ?>>Geneva*</option><option value="Impact, Charcoal, sans-serif" <?php selected($font_family_post_title, 'Impact, Charcoal, sans-serif' ); ?>>Impact</option><option value="Courier, Courier New, monospace" <?php selected($font_family_post_title, 'Courier, Courier New, monospace' ); ?>>Courier</option><option value="Abel" <?php selected($font_family_post_title, 'Abel' ); ?>>Abel</option>
								</optgroup>
                            </select>
                        </div>
						
						
                       
                    </div>
                </div>
            </div><br>
            <!--div end to display "title and fonts colos and formats" -->
            <!--div start for display "Description" Font & Color" -->
            <div class="option option-checbox">
                  <h3 ><?php _e('"Description" Font & Color');?><span class="icon help" style="float:right">
                  <span class="tooltip">Customize font, size and color of description</span></h3>
            </div>
            <!--div end for display "Description" Font & Color" -->
            <!--div start for display "Description" Font & Color with formats and colors display" -->
            <div class="option option-background-upload">
                <div class="section">
                    <div class="element">
					
						<h4 style="font-size:15px;">
						Description font-color
						</h4>
						<div class="form-item">
                            <input type="text"   class="my-color-field" data-default-color="#ffffff" id="descrip_color" name="color_post_entry" value="<?php $value=get_option('soon_page_desgin_settings'); if($value['descrip_color']=='') { echo '#ffffff' ;} else{ echo $value['descrip_color'];}?>" />
                        </div>
                      
						<br>
						
						<div  style="width:20%;display:inline" >
						<h4 style="font-size:15px;">
						Description font-size
						</h4>
						</div>
             	        <div class="select_wrapper background-attachment" style="width:40%">
				            <select name="post_entry_px" id="pxselect_post_entry" class="select">
								<?php 
								for ($i = 10; $i <= 100; $i++) { 
								?>
                                <option value="<?php echo $i; ?>" <?php $value=get_option('soon_page_desgin_settings'); if ( $value['description_font_size'] == $i ) echo selected($value['description_font_size'], $i ); ?> name=""><?php echo $i; ?></option>
							    <?php } ?>
					        </select>
				        </div>
					    <div class="select_wrapper background-attachment" style="width:20%;margin-left:10px;margin-bottom:7px"">
                            <select  id="main_post_entry" onchange="return change_post_entry()" class="select">
                                  <option value="px"><?php _e('px');?></option>
                            </select>
				        </div>
						<br>
						<div  style="width:20%;display:inline" >
						<h4 style="font-size:15px;">
						Description font-style
						</h4>
						</div>
                        <?php $value=get_option('soon_page_desgin_settings'); $font_family_post_entry = $value['description_font_format']; ?>
	                    <div class="select_wrapper background-attachment" style="width:60%">
                            <select  name="font_family_post_description" class="select" id="font_family_post_description">
								<optgroup label="Select Fonts">
                                <option value="roboto" <?php selected($font_family_post_entry, 'roboto' ); ?>>roboto</option>
                                <option value="Arial, sans-serif" <?php selected($font_family_post_entry, 'Arial, sans-serif' ); ?>>Arial</option>
                                <option  value="Verdana, Geneva, sans-serif" <?php selected($font_family_post_entry, 'Verdana, Geneva, sans-serif' ); ?>>Verdana</option><option value="Trebuchet MS, Tahoma, sans-serif" <?php selected($font_family_post_entry, 'Trebuchet MS, Tahoma, sans-serif' ); ?>>Trebuchet</option><option value="Georgia, serif" <?php selected($font_family_post_entry, 'Georgia, serif' ); ?>>Georgia</option><option value="Times New Roman, serif" <?php selected($font_family_post_entry, 'Times New Roman, serif' ); ?>>Times New Roman</option><option value="Tahoma, Geneva, Verdana, sans-serif" <?php selected($font_family_post_entry, 'Tahoma, Geneva, Verdana, sans-serif' ); ?>>Tahoma</option><option value="Palatino, Palatino Linotype, serif" <?php selected($font_family_post_entry, 'Palatino, Palatino Linotype, serif' ); ?>>Palatino</option><option value="Helvetica Neue, Helvetica, sans-serif" <?php selected($font_family_post_entry, 'Helvetica Neue, Helvetica, sans-serif' ); ?>>Helvetica*</option><option value="Calibri, Candara, Segoe, Optima, sans-serif" <?php selected($font_family_post_entry, 'Calibri, Candara, Segoe, Optima, sans-serif' ); ?>>Calibri*</option><option value="Myriad Pro, Myriad, sans-serif" <?php selected($font_family_post_entry, 'Myriad Pro, Myriad, sans-serif' ); ?>>Myriad Pro*</option><option value="Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif" <?php selected($font_family_post_entry, 'Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif' ); ?>>Lucida</option><option value="Arial Black, sans-serif" <?php selected($font_family_post_entry, 'Arial Black, sans-serif' ); ?>>Arial Black</option><option value="Gill Sans, Gill Sans MT, Calibri, sans-serif" <?php selected($font_family_post_entry, 'Gill Sans, Gill Sans MT, Calibri, sans-serif' ); ?>>Gill Sans*</option><option value="Geneva, Tahoma, Verdana, sans-serif" <?php selected($font_family_post_entry, 'Geneva, Tahoma, Verdana, sans-serif' ); ?>>Geneva*</option><option value="Impact, Charcoal, sans-serif" <?php selected($font_family_post_entry, 'Impact, Charcoal, sans-serif' ); ?>>Impact</option><option value="Courier, Courier New, monospace" <?php selected($font_family_post_entry, 'Courier, Courier New, monospace' ); ?>>Courier</option><option value="Abel" <?php selected($font_family_post_entry, 'Abel' ); ?>>Abel</option>
								</optgroup>
                            </select>
                        </div>
                       
					    
                    </div>
                </div>
            </div><br /><br/>
            <!--div end for display "Description" Font & Color with formats and colors display" -->
			
			
			
		 <!--div start for display "Description" Font & Color" -->
            <div class="option option-checbox">
                  <h3><?php _e('Background Noise Effect');?><span class="icon help" style="float:right">
                  <span class="tooltip">Add noise effect on your background</span></h3>
            </div>
            <!--div end for display "Description" Font & Color" -->
            <!--div start for display "Description" Font & Color with formats and colors display" -->
            <div class="option option-background-upload">
                <div class="section">
                    <div class="element">
					
						<div class="form-item">
                            <input type="radio"  value="0" id="cs_noise_effect" <?php if($value['background_effect']=='0') echo 'checked'?> name="cs_noise_effect"  />&nbsp;None<br>
                        <input type="radio"  value="1"  id="cs_noise_effect" <?php if($value['background_effect']=='1') echo 'checked' ?> name="cs_noise_effect" />&nbsp;Noise
                        </div>
                       
						<br>
						
						
             	       
					    
                    </div>
                </div>
            </div><br /><br/>
            <!--div end for display "Description" Font & Color with formats and colors display" -->
			
            <input type="button" class="button-framework save-options"   value= "<?php _e('Save Changes');?>" onclick="datasave_design_settings()"/>
            <input type="button" class="button-framework reset"  value="<?php _e('Restore Defaults');?>" onclick="reset_data_typography()" />
			&nbsp;<img id="loading_design" style="display:none;
vertical-align:middle;" class="loading" src="<?php echo plugins_url('/images/loading.gif',__FILE__);?>" />
            <div id="success_message" ><?php _e('Data Reset Successfully');?></div>
            <div id="success_message_save"  ><?php _e('Data Save Successfully');?></div>
        </div><!-- end of final div for design settings page -->
<style>
#success_message_save
{
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
#success_message
{
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
