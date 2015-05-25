<?php

/**

 * The Header for our theme.

 *

 * Displays all of the <head> section and everything up till <div id="content">

 *

 * @package zerif

 */

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php bloginfo( 'charset' ); ?>">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?php wp_title( '|', true, 'right' ); ?></title>

<link rel="profile" href="http://gmpg.org/xfn/11">

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php
	wp_head();
?>

</head>



<?php if(isset($_POST['scrollPosition'])): ?>

	<body <?php body_class(); ?> onLoad="window.scrollTo(0,<?php echo $_POST['scrollPosition']; ?>)">

<?php else: ?>

	<body <?php body_class(); ?> >

<?php endif; ?>




<!-- =========================

   PRE LOADER       

============================== -->
<?php
	
 global $wp_customize;

 if(is_front_page() && !isset( $wp_customize )): 
 
	$zerif_disable_preloader = get_theme_mod('zerif_disable_preloader');
	
	if( isset($zerif_disable_preloader) && ($zerif_disable_preloader != 1)):
		 
		echo '<div class="preloader">';

			echo '<div class="status">&nbsp;</div>';

		echo '</div>';
		
	endif;	

endif; ?>


<header id="home" class="header">

	<div id="main-nav" class="navbar navbar-inverse bs-docs-nav" role="banner">

		<div class="container">

			<div class="navbar-header responsive-logo">

				<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">

				<span class="sr-only"><?php _e('Toggle navigation','zerif-lite'); ?></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				</button>

				
				
				<?php

					$zerif_logo = get_theme_mod('zerif_logo');

					if(isset($zerif_logo) && $zerif_logo != ""):

						echo '<a href="'.esc_url( home_url( '/' ) ).'" class="navbar-brand">';

							echo '<img src="'.$zerif_logo.'" alt="'.get_bloginfo('title').'">';
								

						echo '</a>';
								

					else:

						echo '<div class="header_title">';	
						
							echo '<h1 class="site-title"><a href="'.esc_url( home_url( '/' ) ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">'.get_bloginfo( 'name' ).'</a></h1>';
							
							echo '<h2 class="site-description"><a href="'.esc_url( home_url( '/' ) ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">'.get_bloginfo( 'description' ).'</a></h2>';
							
							
						echo '</div>';
						
					endif;

				?>

				

			</div>


			
			<nav class="navbar-collapse bs-navbar-collapse collapse" role="navigation" id="site-navigation">
			 
			 
			   <a id="menu-toggle-search"> <div class="navbar-right-search"></div>
			    
				
				<?php wp_nav_menu( array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav navbar-nav navbar-right responsive-nav main-nav-list' ,'fallback_cb'     => 'zerif_wp_page_menu')); ?>
				
			</nav>

		</div>
			
			<div id="wrapper">
				<div class="header-search"> 
				
					<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label>
					<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'zerif-lite' ); ?></span>
					<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'zerif-lite' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'zerif-lite' ); ?>">
					</label>
						<input type="submit" class="header-search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'zerif-lite' ); ?>">
					</form>
				
				
				</div>
			</div>
		
	</div>
	


	<!-- / END TOP BAR -->
