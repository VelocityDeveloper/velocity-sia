<?php
$user_id = isset($_GET['id'])? $_GET['id'] : '';
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$current_id = get_current_user_id();
if ($user_id) {
$user_meta=get_userdata($user_id);
///jika id user ada
if ($user_meta) {
	$user_roles=$user_meta->roles;

	if (($user_roles[0]=='administrator') || (($user_roles[0]=='dosen') && (get_option('foto_profil_dosen')=='ya') && ($user_id==$current_id)) || (($user_roles[0]=='mahasiswa') && (get_option('foto_profil_mahasiswa')=='ya') && ($user_id==$current_id))) {

		$allowed_file_size = 500000; // Allowed file size -> 500kb
		$upload_errors = '';
		$profile_image_id = get_user_meta($user_id,'profile_image',true);
		$url_image_id = wp_get_attachment_url( $profile_image_id );

		// Check that the nonce is valid, and the user can edit this post.
		if (
			isset( $_POST['my_image_upload_nonce'], $_POST['post_id'] )
			//&& wp_verify_nonce( $_POST['my_image_upload_nonce'], 'my_image_upload' )
			//&& is_user_logged_in()
		) {
			$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG ); // IMAGETYPE_GIF
			$detectedType = exif_imagetype($_FILES['my_image_upload']['tmp_name']);
			$error = !in_array($detectedType, $allowedTypes);
				// The nonce was valid and the user has the capabilities, it is safe to continue.

				// These files need to be included as dependencies when on the front end.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );

				// Check file type
				if ( $error ) {
					$upload_errors .= '<p><font color="red">Gagal: Tipe file harus jpeg atau png.</font></p>';
				}

				// Check file size
				if ( $_FILES['my_image_upload']['size'] > $allowed_file_size ) {
					$upload_errors .= '<p><font color="red">Gagal: File yang diupload terlalu besar. Maksimal ukuran file adalah 500KB.</font></p>';
				}
			if ( empty( $upload_errors ) ) {
				if ( $_FILES['my_image_upload']['error'] !== UPLOAD_ERR_OK ) __return_false();
				// Let WordPress handle the upload.
				// Remember, 'my_image_upload' is the name of our file input in our form above.
				$attachment_id = media_handle_upload( 'my_image_upload', $_POST['post_id'] );

				if ( is_wp_error( $attachment_id ) ) {
					// There was an error uploading the image.
					echo "<font color='red'>Error, silahkan coba lagi</font>.<br/>";
				} else {
					if ($profile_image_id > 0) {
						wp_delete_attachment( $profile_image_id  );
					}
					// The image was uploaded successfully!
					update_user_meta($user_id,'profile_image',$attachment_id);
					echo "<font color='green'>Foto profil berhasil diubah</font>.<br/>";
					echo '<script>window.setTimeout(function(){
									window.location.href = "'.$actual_link.'";
								}, 500);</script>';
				}
			} else {  // endif empty($upload_errors)
					echo $upload_errors;
			}  // endif empty($upload_errors)
		}
		?>

		<?php echo '<h6 class="mb-3">'.elv_nama($user_id).'</h6>'; ?>

		<div class="mb-4">
		<?php
		if (($profile_image_id == 0) || empty($url_image_id)) {  ?>
			<img src="<?php echo VELOCITY_SIA_PLUGIN_DIR_URI; ?>/images/no-photo.jpg" class="avatar-elv"/>
		<?php } else { ?>
			<?php echo wp_get_attachment_image( $profile_image_id, array('300', '300'), "", array( "class" => "avatar-elv" ) );  ?>
		<?php } ?>
		</div>


		<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">
			<div class="form-group mb-3">
				<input required type="file" class="form-control-file" name="my_image_upload" id="my_image_upload"  multiple="false" />
				<input type="hidden" name="post_id" id="post_id" value="55" />
				<?php wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' ); ?>
				<small>*Ukuran gambar maksimal 500KB.</small>
			</div>
			<div class="form-group">
				<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" class="btn btn-info" value="Ubah Foto" />
			</div>
		</form>

	<?php }
	} else {
		echo '<div class="alert alert-warning" role="alert"><a href="?halaman=editfoto&id='.$current_id.'" class="alert-link">Ke halaman edit foto</a></div>';
	}
} ?>
