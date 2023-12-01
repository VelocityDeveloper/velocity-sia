<?php
$user_id = get_current_user_id();
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');
$table_materi = $wpdb->prefix . "v_materi";
$table_makul  = $wpdb->prefix . "v_mata_kuliah";
$table_Kelas  = $wpdb->prefix . "v_kelas";

$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id    = isset($_GET['id'])? $_GET['id'] : '' ;

if(current_user_can('administrator')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul");
} if(current_user_can('dosen')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_dosen = $user_id");
}
$tampil_kelas = $wpdb->get_results("SELECT * FROM $table_Kelas");
$sesierrors = '';
//cek file
$allowed_file_size = 15000000; // Ukuran file yang diijinkan -> 15MB

///tambah data
if ((isset($_POST['act']) == "tambah") && ($aksi=='tambah')) {
		$upload_errors = '';
		if ( $_FILES['filemateri']['size'] > $allowed_file_size ) {
			$upload_errors .= '<p><font color="red">'.$_FILES['filemateri']['name'].' Gagal upload : File yang diupload terlalu besar. Maksimal ukuran file adalah 15MB.</font></p>';
		}
		if ( empty( $upload_errors ) ) {
			if ( $_FILES['filemateri']['error'] !== UPLOAD_ERR_OK ) __return_false();
			$attachment_id = media_handle_upload( 'filemateri', 1 );
			if ( is_wp_error( $attachment_id ) ) {
				$idfile = 0;
			} else {
				$idfile = $attachment_id;
			}
		} else {
			echo $upload_errors;
		}
		$detail = array(
		      "nama" 				=> $_POST['nama'],
		      "file" 				=> $idfile,
		      "catatan" 		=> $_POST['catatan'],
		);
		$tujuan = array(
					"mata_kuliah" => $_POST['mata_kuliah'],
					"kelas"				=> $_POST['kelas'],
		);
		$datex = current_time( 'mysql' );
		$datez = date("d-m-Y H:i:s", strtotime($datex));
		$wpdb->insert($table_materi, array(
			'tanggal' => $datez,
			'iduser' 	=> $user_id,
			'tujuan' 	=> json_encode($tujuan),
			'detail' 	=> json_encode($detail),
		  )
		);
		echo '<div class="alert alert-success">Berhasil: materi berhasil ditambahkan.</div>';
}

///edit data
if ((isset($_POST['act']) == "edit") && ($aksi=='edit')) {
	//jika upload file
	if ( $_FILES['filemateri']['size']) {
			$upload_errors = '';
			if ( $_FILES['filemateri']['size'] > $allowed_file_size ) {
				$upload_errors .= '<p><font color="red">'.$_FILES['filemateri']['name'].' Gagal upload : File yang diupload terlalu besar. Maksimal ukuran file adalah 15MB.</font></p>';
			}
			if ( empty( $upload_errors ) ) {
				if ( $_FILES['filemateri']['error'] !== UPLOAD_ERR_OK ) __return_false();
				$attachment_id = media_handle_upload( 'filemateri', 1 );
				if ( is_wp_error( $attachment_id ) ) {
					$idfile = 0;
				} else {
					$idfile = $attachment_id;
				}
			} else {
				echo $upload_errors;
			}
	} else {
		if (isset($_POST['adafile'])) {
			$idfile = $_POST['adafile'];
		} else {
			$idfile = '';
		}
	}
	$detail = array(
				"nama" 				=> $_POST['nama'],
				"file" 				=> $idfile,
				"catatan" 		=> $_POST['catatan'],
	);
	$tujuan = array(
				"mata_kuliah" => $_POST['mata_kuliah'],
				"kelas"				=> $_POST['kelas'],
	);
	$wpdb->update( $table_materi, array(
			'tanggal' => $_POST['tgl'],
			'iduser' 	=> $_POST['user'],
			'tujuan' 	=> json_encode($tujuan),
			'detail' 	=> json_encode($detail),
	), array('id'=> $id));
	echo '<div class="alert alert-success">Berhasil: materi berhasil diupdate.</div>';
}

if (!empty($id) && ($aksi=='edit')) {
	$tampil_materi = $wpdb->get_results("SELECT * FROM $table_materi WHERE id = $id");
	$data = $tampil_materi[0];
	$iduserc			= $data->iduser;
	$tanggalc			= $data->tanggal;
	$tujuant 			= $data->tujuan;
	$detailt 			= $data->detail;
	$detailc 			= json_decode($detailt);
	$nama					= $detailc->nama;
	$filec				= $detailc->file;
	$file					= wp_get_attachment_url( $filec );
	$catatan			= $detailc->catatan;
	$tujuanc 			= json_decode($tujuant);
	$idmatkul			= $tujuanc->mata_kuliah;
	$idkelas			= $tujuanc->kelas;

	//jika login sebagai user, check id user
	if(current_user_can('dosen') || current_user_can('mahasiswa')){
			if ($iduserc != $user_id) {
				$sesierrors .= '<div class="alert alert-danger" role="alert">Maaf anda tidak dapat melihat halaman ini </div>
				<a class="btn btn-secondary btn-sm" href="?halaman=materi">Kembali ke Halaman materi</a>';
			}
	}

} else {
	$filec = '';
	$file = '';
}

//check jika tidak ada eror
if (empty($sesierrors)) {

?>

<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">
		<?php if($aksi=='tambah') {
			echo 'Tambah Materi';
		} else if ($aksi=='edit') {
			echo 'Edit Materi';
		}
		?>
		</div>

		<div class="card-body pb-5">
			<form name="input" method="POST" enctype="multipart/form-data">
			<div class="form-group mb-3">
				<label class="mb-1" for="nama" class="font-weight-bold">Nama Materi</label>
				<input required type="text" class="form-control" name="nama" value="<?php if (isset($nama)) { echo $nama; }?>" id="nama" placeholder="Nama Materi" />
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="filemateri" class="font-weight-bold">File Materi</label>

				<?php if ((isset($file))&&(!empty($file))) {
						echo '<div id="filebox">';
						echo '<a href="'.$file.'" class="h1 d-block"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
						echo '<a id="'.$filec.'" class="gantifile btn btn-primary btn-sm text-white">Ganti File</a>';
						echo '<input type="hidden" name="adafile" value="'.$filec.'" />';
						echo '</div>';
				} ?>
				<div id="fileadd" <?php if ((isset($file))&&(!empty($file))) {echo 'style="display: none;"';}?>>
					<input type="file" class="form-control-file" name="filemateri" id="filemateri"  multiple="false" />
					<small>*Ukuran gambar maksimal 15MB.</small>
				</div>
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="catatan" class="font-weight-bold">Catatan Materi</label>
				<div class="mb-2">
				<?php
					if (isset($catatan)) {
						$content = $catatan;
					} else {
						$content = '';
					}
					$settings  = array(
						'media_buttons' => false,
						'tinymce'       => array(
						        'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
						        'toolbar2'      => '',
						        'toolbar3'      => '',
						    ),
						 );
					$editor_id = 'catatan';
					wp_editor( $content, $editor_id,$settings);
				?>
				</div>
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="mata_kuliah" class="font-weight-bold">Mata Kuliah</label>
				<select class="form-control" name="mata_kuliah" required>
					<option value="">Pilih Mata Kuliah</option>
					<?php foreach ( $tampil_makul as $matkul ) { ?>
						<option value="<?php echo $matkul->id_makul; ?>" <?php if ((isset($idmatkul))&&($matkul->id_makul == $idmatkul)){echo 'selected';};?>><?php echo $matkul->nama_makul; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="kelas" class="font-weight-bold">Kelas</label>
				<div class="form-control">
					<?php foreach ( $tampil_kelas as $kelas ) { ?>
						<div class="checkbox">
							<label><input name="kelas[]" type="checkbox" value="<?php echo $kelas->nama; ?>" <?php if (isset($idkelas)){ if (in_array($kelas->nama, $idkelas)) {echo 'checked';}};?>> <?php echo $kelas->nama; ?> </label>
						</div>
					<?php } ?>
				</div>
			</div>

			<?php if($aksi=='tambah') {
				echo '<input type="hidden" name="act" value="tambah" />';
				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Tambah Materi">';
			} else if ($aksi=='edit') {
				echo '<input type="hidden" name="tgl" value="'.$tanggalc.'" />';
				echo '<input type="hidden" name="user" value="'.$iduserc.'" />';
				echo '<input type="hidden" name="act" value="edit" />';
				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Update materi">';
			}
			?>


			</form>
	</div>
</div>




<script>
jQuery(document).ready(function ($) {
	$('#bataswaktu').datetimepicker({
		format:'d-m-Y H:i:s',
		formatDate:'d-m-Y H:i:s',
		minDate:'-1970/0/0',
		//maxDate:'+1970/0/0',
		timepicker:true,
		step:5,
		scrollMonth : false,
		scrollInput : false
	});
	$(document).on("click",".gantifile",function(e){
		if (confirm('Apakah anda yakin ingin menghapus file sebelumnya?')) {
		var get_id = $(this).attr("id");
		$.ajax({
			type: "POST",
			data: "action=hapusmedia&id=" + get_id,
			url: sia_ajaxurl,
			success:function(data) {
				$("#fileadd").toggleClass("d-block");
				$("#filebox").remove();
			}
		});
		}
	});
});
</script>

<?php } else {
	echo $sesierrors;
} ?>
