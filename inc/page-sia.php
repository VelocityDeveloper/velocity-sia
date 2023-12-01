<?php 

/**
* Template Name: Sistem Informasi Akademik
*/
get_header();
show_admin_bar(false);
$halaman = isset($_GET['halaman'])? $_GET['halaman'] : ''; ?>


<div class="wrapper" id="full-width-page-wrapper">
	<div class="container" id="content">
		<div class="row">
			<div class="col-md-12 content-area" id="primary">
			
				<main class="site-main" id="main" role="main">

				<?php if ( !is_user_logged_in() ) {
					echo '<div style="margin:0 auto;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="border border-info rounded bg-white">
						<div class="text-white bg-info p-2 font-weight-bold">Login</div>';
							echo do_shortcode('[login-elearning]');
					echo '</div> </div>';
				} else { 
					if(current_user_can('administrator')){
						require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/profile-admin.php' );
					} if(current_user_can('mahasiswa')){
						require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/mahasiswa/profile-mahasiswa.php' );
					} if(current_user_can('dosen')){
						require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/dosen/profile-dosen.php' );	
					}
				} ?>	
				
				</main><!-- #main -->
				
			</div><!-- #primary -->
		</div><!-- .row end -->
	</div><!-- Container end -->
</div><!-- Wrapper end -->


<?php get_footer(); ?>