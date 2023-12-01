<?php
$user_id = get_current_user_id();
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');
$table_tugas = $wpdb->prefix . "v_tugas";
$table_prodi = $wpdb->prefix . "v_prodi";
$table_makul = $wpdb->prefix . "v_mata_kuliah";

$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id = isset($_GET['id'])? $_GET['id'] : '' ;

if(current_user_can('administrator')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul");
} if(current_user_can('dosen')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_dosen = $user_id");
}
$sesierrors = '';
//cek file
$allowed_file_size = 15000000; // Ukuran file yang diijinkan -> 15MB

///tambah data
if ((isset($_POST['tambah']) == "ya") && ($aksi=='kerjakan')) {
		$upload_errors = '';
		if ( $_FILES['filetugas']['size'] > $allowed_file_size ) {
			$upload_errors .= '<p><font color="red">'.$_FILES['filetugas']['name'].' Gagal upload : File yang diupload terlalu besar. Maksimal ukuran file adalah 15MB.</font></p>';
		}
		if ( empty( $upload_errors ) ) {
			if ( $_FILES['filetugas']['error'] !== UPLOAD_ERR_OK ) __return_false();
			$attachment_id = media_handle_upload( 'filetugas', 1 );
			if ( is_wp_error( $attachment_id ) ) {
				$idfile = 0;
			} else {
				$idfile = $attachment_id;
			}
		} else {
			echo $upload_errors;
		}
		$detail = array(
		      "file" 				=> $idfile,
		      "catatan" 		=> $_POST['catatan'],
		);
		$datex = current_time( 'mysql' );
		$datez = date("d-m-Y H:i:s", strtotime($datex));
		$wpdb->insert($table_tugas, array(
			'tipe' 		=> 'jawab',
			'tanggal' => $datez,
			'iduser' 	=> $user_id,
			'tujuan' 	=> $id,
			'detail' 	=> json_encode($detail),
		  )
		);
		echo '<div class="alert alert-success">Berhasil: Tugas berhasil disubmit.</div>';
}

///edit data
if ((isset($_POST['edit']) == "ya") && ($aksi=='kerjakan')) {
	//jika upload file
	if ( $_FILES['filetugas']['size']) {
			$upload_errors = '';
			if ( $_FILES['filetugas']['size'] > $allowed_file_size ) {
				$upload_errors .= '<p><font color="red">'.$_FILES['filetugas']['name'].' Gagal upload : File yang diupload terlalu besar. Maksimal ukuran file adalah 15MB.</font></p>';
			}
			if ( empty( $upload_errors ) ) {
				if ( $_FILES['filetugas']['error'] !== UPLOAD_ERR_OK ) __return_false();
				$attachment_id = media_handle_upload( 'filetugas', 1 );
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
				"file" 				=> $idfile,
				"catatan" 		=> $_POST['catatan'],
	);
	$wpdb->update( $table_tugas, array(
			'tipe' 		=> 'jawab',
			'tanggal' => $_POST['tgl'],
			'iduser' 	=> $user_id,
			'tujuan' 	=> $id,
			'detail' 	=> json_encode($detail),
	), array('id'=> $_POST['idkerja']));
	echo '<div class="alert alert-success">Berhasil: Tugas berhasil diupdate.</div>';
}


 //jika id tugas ada
if (!empty($id)) {
	$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE id = $id");
	$data = get_object_vars($tampil_tugas[0]);
	$iduserc			= $data['iduser'];
	$tanggalc			= $data['tanggal'];
  $tanggal 			= date('d M Y, H:i', strtotime($tanggalc));
	$tujuant 			= $data['tujuan'];
	$detailt 			= $data['detail'];
	$detailc 			= json_decode($detailt);
	$nama					= $detailc->nama;
	$filec				= $detailc->file;
	$file					= wp_get_attachment_url( $filec );
	$catatan			= $detailc->catatan;
	$bataswaktuc	= $detailc->bataswaktu;
  $bataswaktu 	= date('d M Y, H:i', strtotime($bataswaktuc));
  $bataswaktux 	= date('Y/m/d H:i:s', strtotime($bataswaktuc));
	$tujuanc 			= json_decode($tujuant);
	$idmatkul			= $tujuanc->mata_kuliah;
	$idkelas			= $tujuanc->kelas;
  $show_makul 	= $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $idmatkul");
  $data_makul 	= get_object_vars($show_makul[0]);

    //cek jika sudah mengerjakan
    		$tampil_jawab = $wpdb->get_results("SELECT * FROM $table_tugas WHERE tujuan = $id and iduser = $user_id");
				if ($tampil_jawab) {
    		$datajawab 		= get_object_vars($tampil_jawab[0]);
				$idkerja			= $datajawab['id'];
				$tanggalkerja	= $datajawab['tanggal'];
				$detailja 		= $datajawab['detail'];
				$detailj 			= json_decode($detailja);
				$catatankerja	= $detailj->catatan;
				$filekerja		= $detailj->file;
				$filekerjac		= wp_get_attachment_url( $filekerja );
			}

		//jika bataswaktu ada
		if ($bataswaktuc) {
    ?>

    <p id="countdown8" class="text-center mb-4"></p>

    <script>
  	jQuery(document).ready(function($) {
  		$('#countdown8').countdown('<?php echo $bataswaktux; ?>')
  		.on('update.countdown', function(event) {
  		  var format = '<div class="btn btn-secondary">%H</div> : <div class="btn btn-secondary">%M</div> : <div class="btn btn-secondary">%S</div>';
  		  if(event.offset.totalDays > 0) {
  		    format = '<div class="btn btn-secondary">%-d Hari </div> - ' + format;
  		  }
  		  if(event.offset.weeks > 0) {
  		    format ='<div class="btn btn-secondary"> %-w Minggu </div> ' + format;
  		  }
  		  $(this).html(event.strftime(format));
  		})
  		.on('finish.countdown', function(event) {
  		    $("#countdown8").html('<div class="alert alert-danger">Waktu Habis</div>');
					$("#formkerjakan").remove();
  		});
  	});
  	</script>

	<?php } //end jika bataswaktu ada ?>

    <!-- Tampilkan detail Tugas -->
    <div class="card mb-4">
      <div class="card-header bg-info text-white font-weight-bold">
        <?php echo $nama; ?>
        <a class="btn btn-sm btn-light absolute-top" data-bs-toggle="collapse" href="#collapsetugas" role="button" aria-expanded="false" aria-controls="collapsetugas">
          <i class="fa fa-chevron-down" aria-hidden="true"></i>
        </a>
      </div>
      <div id="collapsetugas" class="collapse card-body pb-5">
        <table class="table"><tbody>
        <tr><td style="border: 0;">Dosen</td><td style="border: 0;">:</td><td style="border: 0;"><?php echo get_userdata($iduserc)->first_name; ?> </td></tr>
        <tr><td>File</td><td>:</td><td>
        <?php
        //jika file tersedia
        if ((isset($file))&&(!empty($file))) {
            echo '<h1><i class="fa fa-file-text" aria-hidden="true"></i></h1><a href="'.$file.'" class="d-block">Klik untuk unduh</a>';
        } else {
            echo '<span class="fa-stack fa-lg"><i class="fa fa-file-text fa-stack-1x"></i> <i class="fa fa-ban fa-stack-2x text-danger"></i></span> Tidak ada File';
        }
        ?>
        </td></tr>
        <tr><td>Catatan</td><td>:</td><td><?php echo $catatan; ?></td></tr>
        <tr><td>Mata Kuliah</td><td>:</td><td><?php echo $data_makul['nama_makul'] ?></td></tr>
        <tr><td>Kelas</td><td>:</td><td><ul class="pl-3">
          <?php
          foreach ( $idkelas as $kelas ) {
            echo '<li>'.$kelas.'</li>';
          }
          ?>
        </ul></td></tr>
        <tr><td>Tanggal</td><td>:</td><td><?php echo $tanggal; ?></td></tr>
        <tr><td>Batas Waktu</td><td>:</td><td><?php echo $bataswaktu; ?></td></tr>
        </tbody></table>
      </div>
    </div>

    <?php
  } else {
    $sesierrors .='<div class="alert alert-danger"> Tugas belum ditentukan </div>';
  }

    //check jika tidak ada sesi eror
    if (empty($sesierrors)) {

    ?>
        <!-- Tampilkan form pengerjaan Tugas -->
        <div id="formkerjakan" class="card">
        		<div class="card-header bg-info text-white font-weight-bold">Form Pengerjaan</div>

        		<div class="card-body pb-5">
        			<form name="input" method="POST" enctype="multipart/form-data">
	        			<div class="form-group mb-3">
	        				<label for="filetugas" class="font-weight-bold">Lampirkan File</label>

	        				<?php
									//jika ada file
									if ((isset($filekerja))&&(!empty($filekerja))) {
	        						echo '<div id="filebox">';
	        						echo '<a href="'.$filekerjac.'" class="h1 d-block"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
	        						echo '<a id="'.$filekerja.'" class="gantifile btn btn-primary btn-sm text-white">Ganti File</a>';
	        						echo '<input type="hidden" name="adafile" value="'.$filekerja.'" />';
	        						echo '</div>';
	        				} ?>
	        				<div id="fileadd" <?php if ((isset($filekerja))&&(!empty($filekerja))) {echo 'style="display: none;"';}?>>
	        					<input type="file" class="form-control-file" name="filetugas" id="filetugas"  multiple="false" />
	        					<small>*Ukuran gambar maksimal 15MB.</small>
	        				</div>
	        			</div>
	        			<div class="form-group mb-3">
	        				<label for="catatan" class="font-weight-bold">Catatan</label>
									<textarea class="form-control" id="catatan" name="catatan" rows="3"><?php if (isset($catatankerja)) { echo $catatankerja; } ?></textarea>
	        			</div>

	        			<?php if ($tampil_jawab) {
							echo '<input type="hidden" name="idkerja" value="'.$idkerja.'" />';
	        				echo '<input type="hidden" name="tgl" value="'.$tanggalkerja.'" />';
	        				echo '<input type="hidden" name="edit" value="ya" />';
	        				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Update">';
	        			} else {
	        				echo '<input type="hidden" name="tambah" value="ya" />';
	        				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Submit">';
						} ?>

        			</form>
        	</div>
      </div>

        <script>
        jQuery(document).ready(function ($) {
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
