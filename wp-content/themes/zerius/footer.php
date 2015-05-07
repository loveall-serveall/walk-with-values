<?php

/**

 * The template for displaying the footer.

 *

 * Contains the closing of the #content div and all content after

 *

 * @package zerif

 */

?>



<footer id="footer">

<div class="container">

	

	<?php
		
		$footer_sections = 0;
	
		$zerif_address = get_theme_mod('zerif_address','Company address');
		$zerif_address_icon = get_theme_mod('zerif_address_icon',get_template_directory_uri().'/images/map25-redish.png');
		
		$zerif_email = get_theme_mod('zerif_email','Company email');
		$zerif_email_icon = get_theme_mod('zerif_email_icon',get_template_directory_uri().'/images/envelope4-green.png');
		
		$zerif_phone = get_theme_mod('zerif_phone','Phone number');
		$zerif_phone_icon = get_theme_mod('zerif_phone_icon',get_template_directory_uri().'/images/telephone65-blue.png');
		
		$zerif_socials_facebook = get_theme_mod('zerif_socials_facebook','#');

		$zerif_socials_twitter = get_theme_mod('zerif_socials_twitter','#');

		$zerif_socials_linkedin = get_theme_mod('zerif_socials_linkedin','#');

		$zerif_socials_behance = get_theme_mod('zerif_socials_behance','#');

		$zerif_socials_dribbble = get_theme_mod('zerif_socials_dribbble','#');
		
		$zerif_socials_reddit = get_theme_mod('zerif_socials_reddit');
		
		$zerif_socials_tumblr = get_theme_mod('zerif_socials_tumblr');
		
		$zerif_socials_pinterest = get_theme_mod('zerif_socials_pinterest');
		
		$zerif_socials_googleplus = get_theme_mod('zerif_socials_googleplus');
			
		$zerif_copyright = get_theme_mod('zerif_copyright');
		
		
		if(!empty($zerif_address) || !empty($zerif_address_icon)):
			$footer_sections++;
		endif;
		
		if(!empty($zerif_email) || !empty($zerif_email_icon)):
			$footer_sections++;
		endif;
		
		if(!empty($zerif_phone) || !empty($zerif_phone_icon)):
			$footer_sections++;
		endif;

		if(!empty($zerif_socials_facebook) || !empty($zerif_socials_twitter) || !empty($zerif_socials_linkedin) || !empty($zerif_socials_behance) || !empty($zerif_socials_dribbble) || 
		!empty($zerif_copyright)):
			$footer_sections++;
		endif;
		
		if( $footer_sections == 1 ):
			$footer_class = 'col-md-3 footer-box one-cell';
		elseif( $footer_sections == 2 ):
			$footer_class = 'col-md-3 footer-box two-cell';
		elseif( $footer_sections == 3 ):
			$footer_class = 'col-md-3 footer-box three-cell';
		elseif( $footer_sections == 4 ):
			$footer_class = 'col-md-3 footer-box four-cell';
		else:
			$footer_class = 'col-md-3 footer-box four-cell';
		endif;
		
		

		/* COMPANY ADDRESS */

		echo '<div class="footer-box-wrap">';

		if(!empty($zerif_address)):

			echo '<div class="'.$footer_class.' company-details">';

				echo '<div class="icon-top red-text">';

					if(!empty($zerif_address_icon)) echo '<img src="'.esc_url($zerif_address_icon).'"</img>';

				echo '</div>';

				echo $zerif_address;

			echo '</div>';

		endif;

		

		/* COMPANY EMAIL */

		
		

		if(!empty($zerif_email)):

			echo '<div class="'.$footer_class.' company-details">';

				echo '<div class="icon-top green-text">';
					
					if(!empty($zerif_email_icon)) echo '<img src="'.esc_url($zerif_email_icon).'"</img>';

				echo '</div>';

				echo $zerif_email;

			echo '</div>';

		endif;

		

		/* COMPANY PHONE NUMBER */

		
		

		if(!empty($zerif_phone)):

			echo '<div class="'.$footer_class.' company-details">';

				echo '<div class="icon-top blue-text">';

					if(!empty($zerif_phone_icon)) echo '<img src="'.esc_url($zerif_phone_icon).'"</img>';

				echo '</div>';

				echo $zerif_phone;

			echo '</div>';

		endif;


			


			if( !empty($zerif_socials_facebook) || !empty($zerif_socials_twitter) || !empty($zerif_socials_linkedin) || !empty($zerif_socials_behance) || !empty($zerif_socials_dribbble) || !empty($zerif_socials_reddit) || !empty($zerif_socials_tumblr) || !empty($zerif_socials_pinterest) || !empty($zerif_socials_googleplus) || !empty($zerif_copyright) || !empty($zerif_socials_youtube) ):
			
					echo '<div class="'.$footer_class.' copyright">';

					if( !empty($zerif_socials_facebook) || !empty($zerif_socials_twitter) || !empty($zerif_socials_linkedin) || !empty($zerif_socials_behance) || !empty($zerif_socials_dribbble) || !empty($zerif_socials_reddit) || !empty($zerif_socials_tumblr) || !empty($zerif_socials_pinterest) || !empty($zerif_socials_googleplus) || !empty($zerif_socials_youtube) ):

						echo '<ul class="social">';

						

						/* facebook */

						if(isset($zerif_socials_facebook) && $zerif_socials_facebook != ""):

							echo '<li><a href="'.esc_url($zerif_socials_facebook).'"><i class="fa fa-facebook"></i></a></li>';

						endif;

						/* twitter */

						if(isset($zerif_socials_twitter) && $zerif_socials_twitter != ""):

							echo '<li><a href="'.esc_url($zerif_socials_twitter).'"><i class="fa fa-twitter"></i></a></li>';

						endif;

						/* linkedin */

						if(isset($zerif_socials_linkedin) && $zerif_socials_linkedin != ""):

							echo '<li><a href="'.esc_url($zerif_socials_linkedin).'"><i class="fa fa-linkedin"></i></a></li>';

						endif;

						/* behance */

						if(isset($zerif_socials_behance) && $zerif_socials_behance != ""):

							echo '<li><a href="'.esc_url($zerif_socials_behance).'"><i class="fa fa-behance"></i></a></li>';

						endif;

						/* dribbble */

						if(isset($zerif_socials_dribbble) && $zerif_socials_dribbble != ""):

							echo '<li><a href="'.esc_url($zerif_socials_dribbble).'"><i class="fa fa-dribbble"></i></a></li>';

						endif;
						
						/* googleplus */
						
						if(isset($zerif_socials_googleplus) && $zerif_socials_googleplus != ""):

							echo '<li><a href="'.esc_url($zerif_socials_googleplus).'"><i class="fa fa-google"></i></a></li>';

						endif;
						
						/* pinterest */
						
						if(isset($zerif_socials_pinterest) && $zerif_socials_pinterest != ""):

							echo '<li><a href="'.esc_url($zerif_socials_pinterest).'"><i class="fa fa-pinterest"></i></a></li>';
							
							
						endif;
						
						/* tumblr */
						
						if(isset($zerif_socials_tumblr) && $zerif_socials_tumblr != ""):

							echo '<li><a href="'.esc_url($zerif_socials_tumblr).'"><i class="fa fa-tumblr"></i></a></li>';

						endif;
						
						/* reddit */
						
						if(isset($zerif_socials_reddit) && $zerif_socials_reddit != ""):

							echo '<li><a href="'.esc_url($zerif_socials_reddit).'"><i class="fa fa-reddit"></i></a></li>';

						endif;
						
						/* youtube */
						
						if( !empty($zerif_socials_youtube) ):
							echo '<li><a target="_blank" href="'.esc_url($zerif_socials_youtube).'"><i class="fa fa-youtube"></i></a></li>';
						endif;

						echo '</ul>';

					endif;	

			

			

					if( !empty($zerif_copyright) ):

						echo esc_attr($zerif_copyright);

					endif;
					
					echo '<div class="zerif-copyright-box"><a class="zerif-copyright" href="https://wordpress.org/themes/zerius/" target="_blank" rel="nofollow">Zerius </a>'.__('powered by','zerif-lite').'<a class="zerif-copyright" href="http://wordpress.org/" target="_blank" rel="nofollow"> WordPress</a></div>';
					
					echo '</div>';
			
			endif;

			echo '</div>';

			?>

</div> <!-- / END CONTAINER -->

</footer> <!-- / END FOOOTER  -->

<?php wp_footer(); ?>



</body>

</html>
