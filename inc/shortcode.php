<?php

// [login-elearning]
function login_elv_func( ){
	ob_start();
	if ( !is_user_logged_in() ) {
		?>	
		<div class="login-elv-box">
			<form class="needs-validation p-3 py-4 position-relative bg-white" id="login-elv" action="login" method="post">
				<p class="status"></p>
				<div class="input-group mb-3 mr-sm-2">
					<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-user-o"></i></div></div>
					<input type="text" class="form-control" id="username" name="username" placeholder="Username atau Email">
				</div>
				<div class="input-group mb-3 mr-sm-2">
					<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
					<input type="password" class="form-control" id="password" name="password" placeholder="Password">
				</div>
				<?php echo velocitysia_display_recaptcha(); ?>
				<span class="btn btn-info text-white mt-2" id="masuk-el" >Masuk</span>
				<?php echo wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
			</form>
			<script>
			jQuery(function ($) {
			// Perform AJAX login on form submit
			$('#masuk-el').click(function(e) {
				var response = grecaptcha.getResponse();
				if(response.length != 0) {
					jQuery("#login-elv p.status").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
					$.ajax({
						type: 'POST',
						url: sia_ajaxurl,
						data: {
							'action': 'loginelv', //calls wp_ajax_nopriv_ajaxloginelv
							'username': $('#login-elv #username').val(),
							'password': $('#login-elv #password').val(),
							'security': $('#login-elv #security').val(),
							'g-recaptcha-response': response,
						},
						success: function(data){
							jQuery("#login-elv p.status").html(data);
						}
					});
				} else {
					jQuery("#login-elv p.status").html('<div class="alert alert-danger">Silahkan konfirmasi recaptcha dahulu</div>');
				}
				e.preventDefault();
			});
			});
			</script>
		</div>
	<?php
	} else {
		$current_user = wp_get_current_user();
		echo '
		<div class="border border-info rounded bg-white p-3">
			<div class="text-white bg-info p-2 font-weight-bold mb-3">Anda Sudah Login</div>
			<div class="login-info">Anda login dengan username <strong> '.$current_user->user_login.' </strong></div>
			<a class="d-block mt-1 text-info" href="'.home_url().'/sia">Kembali ke Sistem Informasi Akademik</a>
			<a class="btn btn-info text-white mt-3" href="'.wp_logout_url( home_url() ).'">Logout</a>
		</div>';
	}
	return ob_get_clean();
}
add_shortcode( 'login-elearning', 'login_elv_func' );