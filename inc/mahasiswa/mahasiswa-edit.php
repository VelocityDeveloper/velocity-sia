<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_prodi = $wpdb->prefix . "v_prodi";
$arr_agama = array('Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu');
$arr_jenkel = array('Laki-Laki', 'Perempuan');

$nama_mahasiswa = isset($_POST['nama_mahasiswa'])? $_POST['nama_mahasiswa'] : '' ;
$tempat_lahir = isset($_POST['tempat_lahir'])? $_POST['tempat_lahir'] : '' ;
$tgl_lahir = isset($_POST['tgl_lahir'])? $_POST['tgl_lahir'] : '' ;
$agama = isset($_POST['agama'])? $_POST['agama'] : '' ;
$jenis_kelamin = isset($_POST['jenis_kelamin'])? $_POST['jenis_kelamin'] : '' ;
$angkatan = isset($_POST['angkatan'])? $_POST['angkatan'] : '' ;
$semester = isset($_POST['semester'])? $_POST['semester'] : '' ;
$alamat = isset($_POST['alamat'])? $_POST['alamat'] : '' ;
$telepon = isset($_POST['telepon'])? $_POST['telepon'] : '' ;
$username = isset($_POST['username'])? $_POST['username'] : '' ;
$id_prodi = isset($_POST['id_prodi'])? $_POST['id_prodi'] : '' ;
$user_id = isset($_GET['user_id'])? $_GET['user_id'] : '' ;
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '' ;
	$user_info = get_userdata($user_id);
	$user_role = implode(', ', $user_info->roles);



if (!(wp_get_current_user()->user_login == "admindemo")){
if (!(wp_get_current_user()->user_login == "17003001")){
	
// Edit Photo Profile

$allowed_file_size = 500000; // Allowed file size -> 500kb
$upload_errors = '';


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
		echo "Error, silahkan coba lagi.<br/>";
	} else {
		// The image was uploaded successfully!
		update_user_meta($user_id,'profile_image',$attachment_id);
		echo "<font color='green'>Foto profil berhasil diubah</font>.<br/>";
	}
} else {  // endif empty($upload_errors)
        echo $upload_errors;
}  // endif empty($upload_errors)
}

}
}




if (isset($_POST['edit_mahasiswa_ini']) == "ya"){
if (!(wp_get_current_user()->user_login == "admindemo")){
if (!(wp_get_current_user()->user_login == "17003001")){
if((isset($_GET['user_id']) && !current_user_can('mahasiswa'))){
update_user_meta($user_id,'angkatan',$_POST['angkatan']);
update_user_meta($user_id,'semester',$_POST['semester']);
update_user_meta($user_id,'id_prodi',$_POST['id_prodi']);
}
update_user_meta($user_id,'first_name',$_POST['nama_mahasiswa']);
update_user_meta($user_id,'tempat_lahir',$_POST['tempat_lahir']);
update_user_meta($user_id,'tgl_lahir',$_POST['tgl_lahir']);
update_user_meta($user_id,'agama',$_POST['agama']);
update_user_meta($user_id,'jenis_kelamin',$_POST['jenis_kelamin']);
update_user_meta($user_id,'alamat',$_POST['alamat']);
update_user_meta($user_id,'telepon',$_POST['telepon']);
echo '<div class="pesanform sukses">Berhasil: Profil "'.$_POST['nama_mahasiswa'].'" berhasil diupdate.</div>';
}
}
} ?>

<script>
jQuery(document).ready(function ($) {
$('#datepicker').datetimepicker({
	onGenerate:function( ct ){
		$(this).find('.xdsoft_date')
			.toggleClass('xdsoft_disabled');
	},
	format:'d-m-Y',
	formatDate:'d-m-Y',
	minDate:'-1970/0/0',
	//maxDate:'+1970/0/0',
	timepicker:false
});
});
</script>


<div class="foto-profil">
<?php $profile_image_id = get_user_meta($user_id,'profile_image',true);
if ($profile_image_id == 0) { ?>
	<img src="<?php echo VELOCITY_SIA_PLUGIN_DIR_URI; ?>/sia/images/no-photo.jpg"/>
<?php } else { ?>
	<?php echo wp_get_attachment_image( $profile_image_id, 'medium' ); ?>
<?php } ?>
</div>


<form class="margin-bottom" id="featured_upload" method="post" action="#" enctype="multipart/form-data">
	<input required type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />
	<input type="hidden" name="post_id" id="post_id" value="55" />
	<?php wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' ); ?>
	<div class="field-info">*Ukuran gambar maksimal 500KB.</div>
	<div><input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Ubah Foto" /></div>
</form>

<?php if(current_user_can('administrator')){
echo "<h2>".get_user_meta($user_id,'first_name',true)."</h2><br>";
} ?>


<ul class="sia-tab-menu">
  <li><a class="tablinks <?php if($aksi=='editprofile'){echo 'tabaktif';};?>" href="<?php the_permalink(); ?>?halaman=editmahasiswa&user_id=<?php echo $user_id; ?>&aksi=editprofile">Edit Profile</a></li>
  <li><a class="tablinks <?php if($aksi=='editakun'){echo 'tabaktif';};?>" href="<?php the_permalink(); ?>?halaman=editmahasiswa&user_id=<?php echo $user_id; ?>&aksi=editakun">Edit Akun</a></li>
</ul>

<?php if($aksi == "editprofile") { ?>

<form class="form-tambah" name="input" method="POST">
<table class="table-tambah">
<tbody>
<tr>
<td>NIM</td>
<td><?php echo $user_info->user_login; ?></td>
</tr>
<tr>
<td>Nama Mahasiswa</td>
<td><input required type="text" name="nama_mahasiswa" value="<?php echo get_user_meta($user_id,'first_name',true); ?>" placeholder="Nama Mahasiswa" /></td>
</tr>
<tr>
<td>Tempat / Tanggal Lahir</td>
<td><input required type="text" name="tempat_lahir" value="<?php echo get_user_meta($user_id,'tempat_lahir',true); ?>" placeholder="Tempat Lahir" />
<input required type="text" name="tgl_lahir" value="<?php echo get_user_meta($user_id,'tgl_lahir',true); ?>" id="datepicker" placeholder="dd-mm-yyyy" /></td>
</tr>
<tr>
<td>Jenis Kelamin</td>
<td>
<select name="jenis_kelamin">
<?php $jenkelsaya = get_user_meta($user_id,'jenis_kelamin',true); ?>
<?php foreach ($arr_jenkel as $hasil) { ?>
	<option value="<?php echo $hasil; ?>" <?php if ($hasil == $jenkelsaya){echo 'selected';};?>><?php echo $hasil; ?></option>
<?php } ?>
</select>
</td>
</tr>
<tr>
<td>Agama</td>
<td>
<select name="agama">
<?php $agamasaya = get_user_meta($user_id,'agama',true); ?>
<?php foreach ($arr_agama as $hasil) { ?>
	<option value="<?php echo $hasil; ?>" <?php if ($hasil == $agamasaya){echo 'selected';};?>><?php echo $hasil; ?></option>
<?php } ?>
</select>
</td>
</tr>
<tr>
<td>Telepon</td>
<td><input required type="text" name="telepon" value="<?php echo get_user_meta($user_id,'telepon',true); ?>" placeholder="No. Telepon" /></td>
</tr>
<tr>
<td>Alamat</td>
<td><textarea required name="alamat" placeholder="Alamat" ><?php echo get_user_meta($user_id,'alamat',true); ?></textarea></td>
</tr>
<tr>
<td>Angkatan</td>
<?php if((isset($_GET['user_id']) && !current_user_can('mahasiswa'))){ ?>
<td><input required type="text" name="angkatan" value="<?php echo get_user_meta($user_id,'angkatan',true); ?>" placeholder="Angkatan Tahun" /></td>
<?php } else { ?>
<td><input class="text-readonly" readonly type="text" name="angkatan" value="<?php echo get_user_meta($user_id,'angkatan',true); ?>" placeholder="Angkatan Tahun" /></td>
<?php } ?>
</tr>
<tr>
<td>Semester</td>
<?php if((isset($_GET['user_id']) && !current_user_can('mahasiswa'))){ ?>
<td><input required type="text" name="semester" value="<?php echo get_user_meta($user_id,'semester',true); ?>" placeholder="Semester" /></td>
<?php } else { ?>
<td><input class="text-readonly" readonly  type="text" name="semester" value="<?php echo get_user_meta($user_id,'semester',true); ?>" placeholder="Semester" /></td>
<?php } ?>
</tr>
<tr>
<td>Program Studi</td>
<td>
<?php $userid_prodi = get_user_meta($user_id,'id_prodi',true);
$show_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
if((isset($_GET['user_id']) && !current_user_can('mahasiswa'))){ ?>
<select name="id_prodi">
<?php foreach ($show_prodi as $tampil){
$idku = $tampil->id_prodi; ?>
	<option value="<?php echo $idku; ?>" <?php if ($userid_prodi == $idku){echo 'selected';};?>><?php echo $tampil->nama_prodi; ?></option>
<?php } ?>
</select>
<?php } else {
$prodi_mhs = $wpdb->get_results("SELECT * FROM $table_prodi WHERE id_prodi = $userid_prodi");
foreach ($prodi_mhs as $tampil){
$idku = $tampil->id_prodi; ?>
<input class="text-readonly" readonly  type="text" value="<?php echo $tampil->nama_prodi; ?>" />
<?php }
 } ?>
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="edit_mahasiswa_ini" value="ya" />
<input type="hidden" name="username" value="<?php echo get_user_meta($user_id,'user_login',true); ?>" />
<input type="submit" name="submit" value="Simpan">
</form>




<?php } if($aksi == "editakun") { // edit akun ?>

<?php
/* Get user info. */
global $current_user, $wp_roles;
//get_currentuserinfo(); //deprecated since 3.1

/* Load the registration file. */
//require_once( ABSPATH . WPINC . '/registration.php' ); //deprecated since 3.1
$error = array();    
/* If profile was saved, update profile. */
if (!(wp_get_current_user()->user_login == "admindemo")){
if (!(wp_get_current_user()->user_login == "17003001")){
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) ) {
		wp_update_user( array( 'ID' => $user_id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
		echo "<font color='green'>Password berhasil diupdate.</font><br/>";
    }

    /* Update user information. */
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] ))) {
            $error[] = __('<font color="red">Format email yang anda masukkan salah. Silahkan coba lagi.</font>', 'profile');
        } elseif(email_exists(esc_attr( $_POST['email'] )) && (esc_attr( $_POST['email'] ) != $user_info->user_email ) ) {
            $error[] = __('<font color="red">Email yang anda masukkan sudah dipakai user lain. Silahkan coba lagi.</font>', 'profile');
        }else{
            wp_update_user( array ('ID' => $user_id, 'user_email' => esc_attr( $_POST['email'] )));
			$berhasil = "ok";
			echo "<font color='green'>Akun berhasil diupdate.</font><br/>";
        }
    }

}
}
}

if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
<form method="post" id="edit-user-form"><table class="table-tambah">
<tbody>
<tr>
<td>Userlogin</td>
<td><input class="text-readonly" type="text" value="<?php echo $user_info->user_login; ?>" readonly /></td>
</tr>
<tr>
<td>Email Login</td>
<?php if ($berhasil == "ok") {?>
<td><input class="text-input" name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" /></td>
<?php } else { ?>
<td><input class="text-input" name="email" type="text" id="email" value="<?php echo $user_info->user_email; ?>" /></td>
<?php } ?>
</tr>
<tr>
<td>Ubah Password</td>
<td><input class="text-input" name="pass1" type="password" id="pass1" placeholder="Masukkan password baru" /></td>
</tr>
</tbody>
</table>
<?php //action hook for plugin and extra fields
do_action('edit_user_profile',$current_user); ?>
<p class="form-submit">
	<input name="updateuser" type="submit" id="updateuser" class="btn" value="Simpan" />
	<?php wp_nonce_field( 'update-user' ) ?>
	<input name="action" type="hidden" id="action" value="update-user" />
</p><!-- .form-submit -->
</form><!-- #adduser -->
<?php } // endif ?>