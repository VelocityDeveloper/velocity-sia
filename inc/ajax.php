<?php

// login
add_action( 'wp_ajax_nopriv_loginelv', 'ajax_login_elv' );
add_action( 'wp_ajax_loginelv', 'ajax_login_elv' );
function ajax_login_elv(){
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );
    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['g-recaptcha-response'] = $_POST['g-recaptcha-response'];
    $info['remember'] = true;
    if (is_ssl()) {
        $sll = true;
    } else {
        $sll = false;
    }
    $user_signon = wp_signon( $info, $sll );
	if ( is_wp_error($user_signon) ){
        if($user_signon->get_error_message()) {
            echo $user_signon->get_error_message();
        }
		echo '<div class="alert alert-danger" role="alert">Username atau password salah</div>';
	} else {
		echo '<div class="alert alert-success" role="alert"><i class="fa fa-spinner fa-pulse fa-fw"></i> Berhasil, sedang mengalihkan...</div>';
		echo '<script>window.setTimeout(function(){window.location.href = "'.home_url().'";}, 1000);</script>';
	}
    die();
}


add_action('wp_ajax_hapuskelas', 'hapuskelas_ajax');
function hapuskelas_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_kelas";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id' => $_POST['id'],));
	}
	wp_die();
}


add_action('wp_ajax_hapusruang', 'hapusruang_ajax');
function hapusruang_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_ruang";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id' => $_POST['id'],));
	}
	wp_die();
}


add_action('wp_ajax_hapusmakul', 'hapusmakul_ajax');
function hapusmakul_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_mata_kuliah";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id_makul' => $_POST['id'],));
	}
	wp_die();
}

add_action('wp_ajax_hapusjadwal', 'hapusjadwal_ajax');
function hapusjadwal_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_jadwal";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id_jadwal' => $_POST['id'],));
	}
	wp_die();
}


add_action('wp_ajax_hapusfakultas', 'hapusfakultas_ajax');
function hapusfakultas_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_fakultas";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id_fakultas' => $_POST['id'],));
	}
	wp_die();
}

//hapus prodi
add_action('wp_ajax_hapusprodi', 'hapusprodi_ajax');
function hapusprodi_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_prodi";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';

	if($jalankan=='ya'){
		$wpdb->delete($table_name, array('id_prodi' => $_POST['id'],));
	}
	wp_die();
}

//hapus user
add_action('wp_ajax_hapususer', 'hapususer_ajax');
	function hapususer_ajax() {
	$id = isset($_POST['id'])?$_POST['id'] : '';
	$jalankan = isset($_POST['jalankan'])? $_POST['jalankan'] : '';
	if($jalankan=='ya'){
		wp_delete_user($id);
	}
}

add_action('wp_ajax_ubahmahasiswa', 'ubahmahasiswa_ajax');
function ubahmahasiswa_ajax() {
	$user_id = isset($_POST['id'])?$_POST['id'] : '';
	$status = get_user_meta($user_id,'status',true);
	if($status=='Aktif'){
		update_user_meta( $user_id, 'status', 'Tidak Aktif' );
	} else {
		update_user_meta( $user_id, 'status', 'Aktif' );
	}
}


add_action('wp_ajax_pilihmakul', 'pilihmakul_ajax');
function pilihmakul_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_mata_kuliah";
	$id = isset($_POST['id'])?$_POST['id'] : '';
	if ($id) {
		$ijfddik = $wpdb->get_results("SELECT * FROM $table_name WHERE id_makul = $id");
		foreach($ijfddik as $key) { ?>
		<div class="table-responsive">
			<table class="table table-bordered bg-info2">
			<tbody>
			<tr>
			<td>Tahun Akademik</td>
			<td><?php echo $key->tahun_akademik; ?></td>
			</tr>
			<tr>
			<td>Semester</td>
			<td><?php echo $key->semester; ?></td>
			</tr>
			<tr>
			<td>SKS</td>
			<td><?php echo $key->sks; ?></td>
			</tr>
			<tr>
			<td>Jenis Makul</td>
			<td><?php echo $key->jenis_makul; ?></td>
			</tr>
			</tbody>
			</table>
		</div>
		<?php }
	}
	wp_die();
}

add_action('wp_ajax_pengaturan', 'pengaturan_ajax');
function pengaturan_ajax() {
	$id 				= isset($_POST['id'])?$_POST['id'] : '';
	$deprecated = null;
	$autoload 	= 'no';
	$option 		= get_option($id);
	if ($option) {
		if ($option=='ya') {
			update_option($id, 'tidak' );
			echo '<i class="fa fa-toggle-off fa-2x"></i>';
		} else {
			update_option($id, 'ya' );
			echo '<i class="fa fa-toggle-on fa-2x"></i>';
		}
	} else {
		add_option( $id, 'ya', $deprecated, $autoload );
		echo '<i class="fa fa-toggle-on fa-2x"></i>';
	}
	wp_die();
}

//hapus tugas
add_action('wp_ajax_hapustugas', 'hapustugas_ajax');
function hapustugas_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_tugas";
	$id 				= isset($_POST['id'])?$_POST['id'] : '';
	//hapus file
	$show_data 	= $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");
	$datac 			= $show_data[0];
	$detailc 		= json_decode($datac->detail);
	if($detailc->file){
		wp_delete_attachment( $detailc->file );
	}
	//hapus data tugas
	$wpdb->delete($table_name, array('id' => $_POST['id'],));
		//hapus file jawaban tugas
		$show_data 	= $wpdb->get_results("SELECT * FROM $table_name WHERE tujuan = $id");
		$detailc 		= json_decode($show_data[0]->detail);
		if($detailc->file){
			wp_delete_attachment( $detailc->file );
		}
		//hapus data jawaban tugas
		$wpdb->delete($table_name, array('tujuan' => $_POST['id'],));
	wp_die();
}

//lihat tugas
add_action('wp_ajax_lihattugas', 'lihattugas_ajax');
add_action( 'wp_ajax_nopriv_lihattugas', 'lihattugas_ajax' );
function lihattugas_ajax() {
	$id 					= isset($_POST['id'])?$_POST['id'] : '';
	require_once( VELOCITY_SIA_PLUGIN_DIR . 'inc/tugas/tugas-detail.php' );
	wp_die();
}

//hapus materi
add_action('wp_ajax_hapusmateri', 'hapusmateri_ajax');
function hapusmateri_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_materi";
	$id 				= isset($_POST['id'])?$_POST['id'] : '';
	//hapus file
	$show_data 	= $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");
	$datac 			= $show_data[0];
	$detailc 		= json_decode($datac->detail);
	wp_delete_attachment( $detailc->file );
	//hapus data tugas
	$wpdb->delete($table_name, array('id' => $_POST['id'],));
	wp_die();
}

//lihat tugas
add_action('wp_ajax_lihatmateri', 'lihatmateri_ajax');
add_action( 'wp_ajax_nopriv_lihatmateri', 'lihatmateri_ajax' );
function lihatmateri_ajax() {
	$id	= isset($_POST['id'])?$_POST['id'] : '';
	require_once( VELOCITY_SIA_PLUGIN_DIR . 'inc/materi/materi-detail.php' );
	wp_die();
}

//hapus post dari session
add_action('wp_ajax_hapussoal', 'hapussoal_ajax');
function hapussoal_ajax() {
	$id = isset($_POST['idsoal'])?$_POST['idsoal'] : '';
	unset($_SESSION['buatquiz']['pertanyaan'][$id]);
	wp_die();
}

//hapus quiz
add_action('wp_ajax_hapusquiz', 'hapusquiz_ajax');
function hapusquiz_ajax() {
	global $wpdb;
	$table_name = $wpdb->prefix . "v_quiz";
	$id 				= isset($_POST['id'])?$_POST['id'] : '';
	//hapus data quiz
	$wpdb->delete($table_name, array('id' => $_POST['id'],));
	//hapus data jawaban quiz
	$wpdb->delete($table_name, array('tujuan' => $_POST['id'],));
	wp_die();
}

//lihat quiz
add_action('wp_ajax_lihatquiz', 'lihatquiz_ajax');
add_action( 'wp_ajax_nopriv_lihatquiz', 'lihatquiz_ajax' );
function lihatquiz_ajax() {
	$id	= isset($_POST['id'])?$_POST['id'] : '';
	require_once( VELOCITY_SIA_PLUGIN_DIR . 'inc/quiz/quiz-detail.php' );
	wp_die();
}

//input quiz
add_action('wp_ajax_inputquiz', 'inputquiz_ajax');
add_action( 'wp_ajax_nopriv_inputquiz', 'inputquiz_ajax' );
function inputquiz_ajax() {
	global $wpdb;
	$datenow 		= date( 'd-m-Y H:i:s', current_time( 'timestamp', 0 ) );
	$detail 		= isset($_POST['detail']) ? $_POST['detail'] : '';
	$id			 	= $detail['idquiz'];
	$table_quiz 	= $wpdb->prefix . "v_quiz";
	$tampil_quiz  	= $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
	$data 			= $tampil_quiz[0];
	$detailt 		= $data->detail;
	$detailc 		= json_decode($detailt);
	foreach ($detailc->pertanyaan as $idsoal => $value) {
		if (isset($detail[$idsoal])) {
				if ($value->benar == $detail[$idsoal]) {
					$benar[] = $idsoal;
				} else {
					$salah[] = $idsoal;
				}
				$jawaban[$idsoal] = array ($idsoal => $detail[$idsoal]);
		} else {
				$tidakdijawab[] = $idsoal;
				$jawaban[$idsoal] = array ($idsoal => 0);
		}
	}
	$jawabbenar = $benar ? count($benar) : 0;
	$jawabsalah = $salah ? count($salah) : 0;
	$jawabtidakdijawab = $tidakdijawab ? count($tidakdijawab) : 0;
	$jumlahsoal = $detail['jumlahsoal'];
	$nilaix  = 100 / $jumlahsoal * $jawabbenar;
	$nilai 	= (round($nilaix,1));
	echo '<div class="card mx-auto" style="width: 18rem;">
			  <div class="card-body text-center bg-nilai">
			    <h5 class="card-title">Nilai anda : </h5>
			    <p class="card-text h1" style="font-size: 7rem;">'.$nilai.'</p>
			  </div>
			  <ul class="list-group list-group-flush">
			    <li class="list-group-item"><i class="fa fa-check text-success"></i> Benar = '.$jawabbenar.'</li>
			    <li class="list-group-item"><i class="fa fa-close text-danger"></i> Salah = '.$jawabsalah.'</li>
			    <li class="list-group-item"><i class="fa fa-question-circle text-warning"></i> Tidak dijawab = '.$jawabtidakdijawab.'</li>
					<li class="list-group-item"><i class="fa fa-wpforms"></i> Jumlah Soal = '.$jumlahsoal.'</li>
			  </ul>
				<div class="card-footer">
				    <a href="?halaman=quiz" class="btn btn-success btn-lg btn-block">Selesai </a>
  			</div>
			</div>';
	if(current_user_can('mahasiswa')){
			//simpan jawaban ke database
			$detailjawab = array(
						"nilai" 				=> $nilai,
						"jawaban" 			=> $jawaban,
						"benar" 				=> $jawabbenar,
						"salah" 				=> $jawabsalah,
						"tidakdijawab" 	=> $jawabtidakdijawab,
						"waktuawal" 		=> $_SESSION['kerjaquiz']['setwaktuawal'],
						"waktuakhir" 		=> $datenow,
			);
			$wpdb->insert($table_quiz, array(
				'tipe' 		=> 'jawab',
				'tanggal' => $datenow,
				'iduser' 	=> $detail['iduser'],
				'tujuan' 	=> $detail['idquiz'],
				'detail' 	=> json_encode($detailjawab),
			  )
			);
	}
	unset($_SESSION['kerjaquiz']);
	wp_die();
}

//jawab soal
add_action('wp_ajax_jawabsoal', 'jawabsoal_ajax');
add_action( 'wp_ajax_nopriv_jawabsoal', 'jawabsoal_ajax' );
function jawabsoal_ajax() {
	$id			= isset($_POST['id'])?$_POST['id'] : '';
	$value	= isset($_POST['value'])?$_POST['value'] : '';
	$_SESSION['kerjaquiz']['jawab'][$id] = $value;
	wp_die();
}

//lihat hasil quiz
add_action('wp_ajax_lihathasilquiz', 'lihathasilquiz_ajax');
add_action( 'wp_ajax_nopriv_lihathasilquiz', 'lihathasilquiz_ajax' );
function lihathasilquiz_ajax() {
	$id		= isset($_POST['id'])?$_POST['id'] : '';
	require_once( VELOCITY_SIA_PLUGIN_DIR . 'inc/quiz/quiz-detail-hasil.php' );
	wp_die();
}

//hapus media
add_action('wp_ajax_hapusmedia', 'hapusmedia_ajax');
function hapusmedia_ajax() {
	$id = isset($_POST['id'])?$_POST['id'] : '';
	wp_delete_attachment( $id );
	wp_die();
}


?>
