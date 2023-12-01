<?php


//this function enqueues all scripts required for media uploader to work
function enqueue_media_uploader() {
    wp_enqueue_media();
}
add_action("wp_enqueue_scripts", "enqueue_media_uploader");

//Get ajax
add_action('wp_head','velocity_sia_ajaxurl');
function velocity_sia_ajaxurl() {
	$html = '<script type="text/javascript">';
	$html .= 'var sia_ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
	$html .= '</script>';
	echo $html;
}

// disable dashboard access
add_action('admin_init', 'disable_dashboard');
function disable_dashboard() {
	if (current_user_can('pending') || wp_get_current_user()->user_login == "admindemo") {
		wp_redirect(home_url());
		exit;
	}
}

// display recaptcha
function velocitysia_display_recaptcha() {
    echo '<div class="velocitysia-recaptcha my-2">';
        if (class_exists('Velocity_Addons_Captcha')){
            $captcha = new Velocity_Addons_Captcha;
            $captcha->display();
        }
    echo '</div>';
}


//tampilkan menu
function elv_menu($args) {
	$html ='';
	$idx = 1;
	$hal = $args['halaman_sekarang'];
	$html .='<ul id="elv-menu" class="nav d-block">';
		if($hal) { $class1 = ''; } else { $class1 = 'active'; }
		$html .='<li class="nav-item '.$class1.'"><a class="nav-link" href="'.home_url().'/sia">Beranda</a></li>';
		foreach ($args['menu'] as $menu) {
			$id = $idx++;
			if ($menu['halaman']==$hal) { $class = 'active'; } else { $class = ''; }
			if ($menu['halaman']=='#') { $pager = 'onclick="return false"'; } else { $pager = ''; }

			$html .= '<li class="nav-item main-nav-item '.$class.'" id="elvm-'.$id.'">';
			$html .= '<a class="nav-link" href="?halaman='.$menu['halaman'].'" '.$pager.'>'.$menu['judul'].'</a>';

				//sub menu
				if (isset($menu['submenu']) && $menu['submenu']) {
					$numx = 1;
					$html .= '<a class="btn dropdown-toggle dropdown-toggle-split" href="#dropdownMenu'.$id.'" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="dropdownMenu'.$id.'">
							<span class="sr-only">Toggle Dropdown</span>
							</a>';
					$html .= '<ul class="dropdownmenu collapse" id="dropdownMenu'.$id.'">';
					foreach ($menu['submenu'] as $submenu) {
						$ids = $id.'-'.$numx++;
						if ($submenu['halaman']==$hal) { $class = 'active'; } else { $class = ''; }
						$html .= '<li class="nav-item '.$class.'" id="elvm-'.$ids.'">';
						$html .= '<a class="nav-link" href="?halaman='.$submenu['halaman'].'">'.$submenu['judul'].'</a>';
						$html .= '</li>';
					}
					$html .= '</ul>';
				}

			$html .= '</li>';
		}
		$html .= '<li class="nav-item"><a class="nav-link" href="'.wp_logout_url(home_url().'').'">Logout</a></li>';
	$html .= '</ul>';
	return $html;
}

//tampilkan nama user
function elv_nama($id) {
	$html ='';
	$user_info = get_userdata($id);
	$nama = get_user_meta($id,'first_name',true);
	if ($nama) {
		$html .= $nama ;
	} else {
		$html .= $user_info->user_login;
	}
	return $html;
}


//foto profil
function elv_fotouser($id) {
	$html ='';
	$user_meta=get_userdata($id);
	$user_roles=$user_meta->roles;
	$profile_image_id = get_user_meta($id,'profile_image',true);
	$url_image_id = wp_get_attachment_url( $profile_image_id );
	$html .= '<div class="avatar-elv-box">';
	if (($profile_image_id == 0) || empty($url_image_id)) { 
		$html .='<img src="'.VELOCITY_SIA_PLUGIN_DIR_URI.'/images/no-photo.jpg" class="avatar-elv"/>';
	} else { 
		$html .= wp_get_attachment_image( $profile_image_id, array('300', '300'), "", array( "class" => "avatar-elv" ) );
	}
	if (($user_roles[0]=='administrator') || (($user_roles[0]=='dosen') && (get_option('foto_profil_dosen')=='ya')) || (($user_roles[0]=='mahasiswa') && (get_option('foto_profil_mahasiswa')=='ya'))) {
		$html .= '<a class="avatar-elv-link" href="?halaman=editfoto&id='.$id.'">Ganti foto</a>';	
	}
	$html .= '</div>';
	return $html;
}

//form tambah user
function elv_tambahuser($args) {	
	$role = $args['role'];
	if (isset($_POST['user_login']) && isset($_POST['user_email']) ) {
		$user_exist = username_exists( $_POST['user_login'] );
		if ( !$user_exist and email_exists($_POST['user_email']) == false ) {
			$default_newuser = array(
				'user_pass' =>  $_POST['user_pass'],
				'user_login' => $_POST['user_login'],
				'user_email' => $_POST['user_email'],
				'first_name' => $_POST['first_name'],
				'role' => $role,
			);
			$user_id = wp_insert_user($default_newuser);
			if ( $user_id && !is_wp_error( $user_id ) ) {
				foreach ($args['fields'] as $fields) {
					$ids = $fields['id'];
					${$ids} = $_POST[$ids];
					if (($fields['id'] != "user_pass" || $fields['id'] != "user_login" || $fields['id'] != "user_email" || $fields['id'] != "first_name")) {
						add_user_meta( $user_id, $ids, ${$ids}, true );
					}
				}
				echo '<div class="alert alert-success"> Data berhasil ditambah.</div>';	
			} else {
				echo '<div class="alert alert-danger"> Gagal, silahkan coba kembali.</div>';		
			}
		} elseif ($user_exist) {
			echo '<div class="alert alert-danger">Gagal. '.$_POST['user_login'].'" sudah ada.</div>';
		} elseif (email_exists($_POST['user_email'])) {
			echo '<div class="alert alert-danger">Gagal. '.$_POST['user_email'].'" sudah dipakai.</div>';
		} else {
			echo '<div class="alert alert-danger">Gagal, silahkan coba kembali.</div>';
		}
	}
	echo '<form name="input" method="POST">';
	foreach ($args['fields'] as $fields) {
		echo '<div class="mb-3 form-group fields-'.$fields['id'].'">';	
		
			if ($fields['type']!=='hidden') {
				echo '<label for="'.$fields['id'].'" class="mb-1 font-weight-bold">'.$fields['title'].'</label>';
			}
			
			if (isset($fields['required']) && $fields['required']==true) { $req = 'required'; } else { $req = ''; }
			if (isset($fields['readonly']) && $fields['readonly']==true) { $read = 'readonly'; } else { $read = ''; }
			if (isset($fields['default'])) { $value = $fields['default']; } else { $value = ''; }
			
			//type input text
			if ($fields['type']=='text') {
				echo '<input type="text" id="'.$fields['id'].'" value="'.$value.'" class="form-control" name="'.$fields['id'].'" placeholder="'.$fields['title'].'" '.$req.' '.$read.'>';
			}
			//type input textarea
			if ($fields['type']=='textarea') {
				echo '<textarea id="'.$fields['id'].'" class="form-control" name="'.$fields['id'].'" '.$req.' '.$read.'>'.$value.'</textarea>';
			} 
			//type input email
			else if ($fields['type']=='email') {
				echo '<input type="email" id="'.$fields['id'].'" value="'.$value.'" pattern="[^ @]*@[^ @]*" class="form-control" name="'.$fields['id'].'" placeholder="'.$fields['title'].'" '.$req.' '.$read.'>';
			} 
			//type input date
			else if ($fields['type']=='date') {
				echo '<input type="text" id="'.$fields['id'].'" value="'.$value.'" class="form-control datepicker" name="'.$fields['id'].'" '.$req.' '.$read.'>';
			}  
			//type input password
			else if ($fields['type']=='password') {
				echo '<input type="password" id="'.$fields['id'].'" class="pass1 form-control" name="'.$fields['id'].'" '.$req.'>';
				echo '<small>Ketik Ulang</small>';
				echo '<input type="password" id="re'.$fields['id'].'" onkeyup="checkPass(); return false;" class="pass2 form-control" name="re'.$fields['id'].'" '.$req.'>';
				echo '<small id="passN"></small>';
			} 
			//type input option
			else if ($fields['type']=='option') {
				echo '<select id="'.$fields['id'].'" class="form-control" name="'.$fields['id'].'" '.$req.'>';
					foreach ($fields['option'] as $option1 => $option2 ) {
						echo '<option value="'.$option1.'"';
						if ($value==$option1) { echo 'selected';}
						echo '>'.$option2.'</option>';
					}
				echo '</select>';
			}  			
			
			//type input hidden
			if ($fields['type']=='hidden') {
				echo '<input type="hidden" id="'.$fields['id'].'" value="'.$value.'" name="'.$fields['id'].'">';
			}
		
			if (isset($fields['desc'])) {
				echo '<small class="text-primary">'.$fields['desc'].'</small>';				
			}
			
		echo '</div>';
	}	
	echo '<input type="hidden" name="tambah" value="yes">';
	echo '<input type="submit" class="btn btn-info" name="submit" value="Tambah"></form>';
	?>
	<script>
		jQuery(document).ready(function ($) {
			$('.datepicker').datetimepicker({
				onGenerate:function( ct ){
					$(this).find('.xdsoft_date')
					.toggleClass('xdsoft_disabled');
				},
				format:'d-m-Y',
				formatDate:'d-m-Y',
				minDate:'-1970/0/0',
				//maxDate:'+1970/0/0',
				timepicker:false,
				scrollMonth : false,
				scrollInput : false
			});
		});
		function checkPass() {
				//Store the password field objects into variables ...
				var pass1 = document.getElementById('user_pass');
				var pass2 = document.getElementById('reuser_pass');
				var message = document.getElementById('passN');
				var goodColor = "#66cc66";
				var badColor = "#ff6666";
				if(pass1.value == pass2.value){
					message.style.color = goodColor;
					message.innerHTML = "Passwords Cocok!"
				}else{
					message.style.color = badColor;
					message.innerHTML = "Passwords tidak cocok"
				}
			}  
	</script>
	<?php
}


//form edit profil user
function elv_edituser($args,$user_id) {
 $user_info = get_userdata($user_id);
 if ($user_info) {
	$role = $args['role'];
	echo '<div class="avatar mb-3"> '.elv_fotouser($user_id).' </div>';
	if (isset($_POST['edit'])) {
		foreach ($args['fields'] as $fields) {
			if (!($fields['id']=="user_pass" || $fields['id']=="user_email" || $fields['id']=="user_login")) {
				$ids = $fields['id'];
				update_user_meta( $user_id, $ids, $_POST[$ids]);
			}
		}
		echo '<div class="alert alert-success"> Data berhasil diupdate.</div>';	
	}
	echo '<form name="input" method="POST">';
	foreach ($args['fields'] as $fields) {
	$value = get_user_meta($user_id,$fields['id'],true);
	if ($fields['id']=="user_login") {
		echo '<div class="form-group fields-'.$fields['id'].'">
		<label class="font-weight-bold">'.$fields['title'].' : <span class="text-info">'.$value.'</span></label>
		</div>';
	}	
	if (!($fields['id']=="user_pass" || $fields['id']=="user_email" || $fields['id']=="user_login")) {
		echo '<div class="mb-3 form-group fields-'.$fields['id'].'">';	
			echo '<label for="'.$fields['id'].'" class="mb-1 font-weight-bold">'.$fields['title'].'</label>';
			if ($fields['required']==true) { $req = 'required'; } else { $req = ''; }
			//type input text
			if ($fields['type']=='text') {
				echo '<input type="text" value="'.$value.'" id="'.$fields['id'].'" class="form-control" name="'.$fields['id'].'" placeholder="'.$fields['title'].'" '.$req.'>';
			}
			//type input textarea
			if ($fields['type']=='textarea') {
				echo '<textarea id="'.$fields['id'].'" class="form-control" name="'.$fields['id'].'" '.$req.'>'.$value.'</textarea>';
			} 
			//type input email
			else if ($fields['type']=='email') {
				echo '<input type="email" value="'.$value.'" id="'.$fields['id'].'" pattern="[^ @]*@[^ @]*" class="form-control" name="'.$fields['id'].'" placeholder="'.$fields['title'].'" '.$req.'>';
			} 
			//type input date
			else if ($fields['type']=='date') {
				echo '<input type="text" id="'.$fields['id'].'" value="'.$value.'" class="form-control datepicker" name="'.$fields['id'].'" '.$req.'>';
			}
			//type input option
			else if ($fields['type']=='option') {
				echo '<select id="'.$fields['id'].'" class="form-control" name="'.$fields['id'].'" '.$req.'>';
					foreach ($fields['option'] as $option1 => $option2 ) {
						echo '<option value="'.$option1.'"';
						if ($value==$option1) { echo 'selected';}
						echo '>'.$option2.'</option>';
					}
				echo '</select>';
			}  
		
			if (isset($fields['desc'])) {
				echo '<small class="text-primary">'.$fields['desc'].'</small>';				
			}
			
		echo '</div>';
	}
	}
	echo '<input type="hidden" name="edit" value="yes">';
	echo '<input type="submit" class="btn btn-info" name="submit" value="Update"></form>';
	?>
	<script>
		jQuery(document).ready(function ($) {
			$('.datepicker').datetimepicker({
				onGenerate:function( ct ){
					$(this).find('.xdsoft_date')
					.toggleClass('xdsoft_disabled');
				},
				format:'d-m-Y',
				formatDate:'d-m-Y',
				minDate:'-1970/0/0',
				//maxDate:'+1970/0/0',
				timepicker:false,
				scrollMonth : false,
				scrollInput : false
			});
		});
	</script>
	<?php
 } else {
	echo '<div class="alert alert-warning">Oops, User tidak ditemukan.</div>';
 }
}


//form edit akun user
function elv_edit_akun_user($args,$user_id) {
 $user_info = get_userdata($user_id);
 if($user_info) {
	$role = $args['role'];
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

			/* Update user password. */
			if ( !empty($_POST['user_pass'] ) ) {
				wp_update_user( array( 'ID' => $user_id, 'user_pass' => esc_attr( $_POST['user_pass'] ) ) );
				echo "<font color='green'>Password berhasil diupdate.</font><br/>";
			}

			/* Update user information. */
			if ( !empty( $_POST['user_email'] ) ){
				if (!is_email(esc_attr( $_POST['user_email'] ))) {
					echo '<font color="red">Format email yang anda masukkan salah. Silahkan coba lagi.</font>';
				} elseif(email_exists(esc_attr( $_POST['user_email'] )) && (esc_attr( $_POST['user_email'] ) != $user_info->user_email ) ) {
					echo '<font color="red">Email yang anda masukkan sudah dipakai user lain. Silahkan coba lagi.</font>';
				}else{
					wp_update_user( array ('ID' => $user_id, 'user_email' => esc_attr( $_POST['user_email'] )));
					$berhasil = "ok";
					echo "<font color='green'>Akun berhasil diupdate.</font><br/>";
				}
			}
			
	}
	echo '<form name="input" method="POST">';
		echo '<div class="mb-3 form-group fields-user_login">';
			echo '<label class="mb-1 font-weight-bold">NIDN: <span class="text-info">'.$user_info->user_login.'</span></label>';
		echo '</div>';
		echo '<div class="mb-3 form-group fields-user_email">';
			echo '<label for="user_email" class="mb-1 font-weight-bold">Email</label>';
			echo '<input type="email" value="'.$user_info->user_email.'" id="user_email" pattern="[^ @]*@[^ @]*" class="form-control" name="user_email" placeholder="Email" required="">';
		echo '</div>';
		echo '<div class="mb-3 form-group fields-user_pass">';
			echo '<label for="user_pass" class="mb-1 font-weight-bold">Password</label>';
			echo '<input type="password" id="user_pass" class="pass1 form-control" name="user_pass" required="">';
			echo '<small>Ketik Ulang</small>';
			echo '<input type="password" id="reuser_pass" onkeyup="checkPass(); return false;" class="pass2 form-control" name="reuser_pass" required="">';
			echo '<small id="passN"></small>';
		echo '</div>';
	echo '<input type="hidden" name="action" value="update-user"> '.wp_nonce_field( 'update-user' ).'';
	echo '<input type="submit" class="btn btn-info" name="submit" value="Update">';
	echo '</form>';
	?>
	<script>
		function checkPass() {
				//Store the password field objects into variables ...
				var pass1 = document.getElementById('user_pass');
				var pass2 = document.getElementById('reuser_pass');
				var message = document.getElementById('passN');
				var goodColor = "#66cc66";
				var badColor = "#ff6666";
				if(pass1.value == pass2.value){
					message.style.color = goodColor;
					message.innerHTML = "Passwords Cocok!"
				}else{
					message.style.color = badColor;
					message.innerHTML = "Passwords tidak cocok"
				}
			}  
	</script>
	<?php
 } else {
	echo '<div class="alert alert-warning">Oops, User tidak ditemukan.</div>';
 }
}

//form lihat profil user
function elv_lihatuser($args,$user_id) {	
  if($user_id) {
	$role = $args['role'];
	echo '<div class="avatar mb-3"> '.elv_fotouser($user_id).' </div>';
	echo '<table class="table">';
	foreach ($args['fields'] as $fields) {
		$value = get_user_meta($user_id,$fields['id'],true);
		if ($fields['id']=="user_login") {
			echo '<tr><td class="font-weight-bold">'.$fields['title'].'</td><td>'.$value.'</td></tr>';
		}	
		if (!($fields['id']=="user_pass" || $fields['id']=="user_email" || $fields['id']=="user_login")) {
			echo '<tr class="fields-'.$fields['id'].'">';	
				echo '<td class="font-weight-bold">'.$fields['title'].'</td>';
				if ($fields['type']=='option') {
					foreach ($fields['option'] as $option1 => $option2 ) {
						if ($value==$option1) { echo '<td>'.$option2.'</td>';}
					}
				} else  {
					echo '<td>'.$value.'</td>';
				}					
			echo '</tr>';
		}
	}
	echo '</table>';
 } else {
	echo '<div class="alert alert-warning">Oops, ID belum ditentukan.</div>';
 }
}



global $wpdb;
//ambil prodi
$table_prodi = $wpdb->prefix . "v_prodi";
$ambil_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
foreach ( $ambil_prodi as $prodi ) {
	$edprodi[$prodi->id_prodi] = $prodi->nama_prodi;
}
$array_prodi = $edprodi;

//ambil prodi
$table_kelas = $wpdb->prefix . "v_kelas";
$ambil_kelas = $wpdb->get_results("SELECT * FROM $table_kelas");
foreach ( $ambil_kelas as $pkelas ) {
	$edkelas[$pkelas->nama] = $pkelas->nama;
}
$array_kelas = $edkelas;

//profil dosen
$profil_dosen = array (
	'role' => 'dosen',
	'fields' => array (
		array(
			'id'       => 'user_login',
			'type'     => 'text',
			'title'    => 'NIDN',
			'desc'     => 'Ini akan dipakai untuk login username.',
			'required'  => true,
		),
		array(
			'id'       => 'first_name',
			'type'     => 'text',
			'title'    => 'Nama Lengkap',
			'required'  => true,
		),
		array(
			'id'       => 'user_email',
			'type'     => 'email',
			'title'    => 'Email',
			'required'  => true,
		),
		array(
			'id'       => 'tempat_lahir',
			'type'     => 'text',
			'title'    => 'Tempat Lahir',
			'required'  => true,
		),
		array(
			'id'       => 'tgl_lahir',
			'type'     => 'date',
			'title'    => 'Tanggal Lahir',
			'required'  => true,
		),
		array(
			'id'       => 'jenis_kelamin',
			'type'     => 'option',
			'title'    => 'Jenis Kelamin',
			'required' => true,
			'option'   => array (
						'Laki-Laki' => 'Laki-Laki',
						'Perempuan' => 'Perempuan',
						),
		),
		array(
			'id'       => 'agama',
			'type'     => 'option',
			'title'    => 'Agama',
			'required' => true,
			'option'   => array (
						'Islam' => 'Islam',
						'Kristen Protestan' => 'Kristen Protestan',
						'Kristen Katolik' => 'Kristen Katolik',
						'Hindu' => 'Hindu',
						'Buddha' => 'Buddha',
						'Konghucu' => 'Konghucu',
						),
		),
		array(
			'id'       => 'telepon',
			'type'     => 'text',
			'title'    => 'Telepon',
			'required'  => true,
		),
		array(
			'id'       => 'alamat',
			'type'     => 'textarea',
			'title'    => 'Alamat',
			'required'  => true,
		),
		array(
			'id'       => 'id_prodi',
			'type'     => 'option',
			'title'    => 'Program Studi',
			'required' => true,
			'option'   => $array_prodi,
		),
		array(
			'id'       => 'user_pass',
			'type'     => 'password',
			'title'    => 'Password',
			'required'  => true,
		),
	),
);


//profil mahasiswa
$profil_mahasiswa = array (
	'role' => 'mahasiswa',
	'fields' => array (
		array(
			'id'       => 'user_login',
			'type'     => 'text',
			'title'    => 'NIM',
			'desc'     => 'Ini akan dipakai untuk login username.',
			'required'  => true,
			'default'  => date("ymdhis",time()),
		),
		array(
			'id'       => 'first_name',
			'type'     => 'text',
			'title'    => 'Nama Lengkap',
			'required'  => true,
		),
		array(
			'id'       => 'user_email',
			'type'     => 'email',
			'title'    => 'Email',
			'required'  => true,
		),
		array(
			'id'       => 'tempat_lahir',
			'type'     => 'text',
			'title'    => 'Tempat Lahir',
			'required'  => true,
		),
		array(
			'id'       => 'tgl_lahir',
			'type'     => 'date',
			'title'    => 'Tanggal Lahir',
			'required'  => true,
		),
		array(
			'id'       => 'jenis_kelamin',
			'type'     => 'option',
			'title'    => 'Jenis Kelamin',
			'required' => true,
			'option'   => array (
						'Laki-Laki' => 'Laki-Laki',
						'Perempuan' => 'Perempuan',
						),
		),
		array(
			'id'       => 'agama',
			'type'     => 'option',
			'title'    => 'Agama',
			'required' => true,
			'option'   => array (
						'Islam' => 'Islam',
						'Kristen Protestan' => 'Kristen Protestan',
						'Kristen Katolik' => 'Kristen Katolik',
						'Hindu' => 'Hindu',
						'Buddha' => 'Buddha',
						'Konghucu' => 'Konghucu',
						),
		),
		array(
			'id'       => 'telepon',
			'type'     => 'text',
			'title'    => 'Telepon',
			'required'  => true,
		),
		array(
			'id'       => 'alamat',
			'type'     => 'textarea',
			'title'    => 'Alamat',
			'required'  => true,
		),
		array(
			'id'       => 'angkatan',
			'type'     => 'text',
			'title'    => 'Angkatan',
			'required'  => true,
			'default'  => date("Y"),
		),
		array(
			'id'       => 'semester',
			'type'     => 'text',
			'title'    => 'Semester',
			'required'  => true,
			'default'  => '1',
		),
		array(
			'id'       => 'id_prodi',
			'type'     => 'option',
			'title'    => 'Program Studi',
			'required' => true,
			'option'   => $array_prodi,
		),
		array(
			'id'       => 'kelas',
			'type'     => 'option',
			'title'    => 'Kelas',
			'required' => true,
			'option'   => $array_kelas,
		),
		array(
			'id'       => 'status',
			'type'     => 'option',
			'title'    => 'Status',
			'required' => true,
			'option'   => array (
						'Aktif' => 'Aktif',
						'Tidak Aktif' => 'Tidak Aktif',
						),
			'default'  => 'Aktif',
		),
		array(
			'id'       => 'user_pass',
			'type'     => 'password',
			'title'    => 'Password',
			'required'  => true,
		),
	),
);
