<?php
$user_id = get_current_user_id();
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_quiz 	= $wpdb->prefix . "v_quiz";
$table_makul 	= $wpdb->prefix . "v_mata_kuliah";
$table_Kelas 	= $wpdb->prefix . "v_kelas";

$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id 	= isset($_GET['id'])? $_GET['id'] : '' ;
$is 	= isset($_GET['is'])? $_GET['is'] : '' ;
$ids 	= isset($_GET['ids'])? $_GET['ids'] : '' ;

$datex 		= current_time( 'mysql' );
$datenow 	= date("d-m-Y H:i:s", strtotime($datex));

if(current_user_can('administrator')){
	$makul_validation = '';
} elseif(current_user_can('dosen')){
	$makul_validation = ' WHERE id_dosen = '.$user_id;
}
$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul".$makul_validation);
$tampil_kelas = $wpdb->get_results("SELECT * FROM $table_Kelas");

$sesierrors = '';

//jika reset
if ((isset($_POST['reset']) == "reset")) {
		unset($_SESSION['buatquiz']);
		echo '<div class="alert alert-success" role="alert">Data Form berhasil diatur ulang</div>';
		echo '<script>window.setTimeout(function(){
						window.location.href = "?halaman=quiz&aksi=tambah";
					}, 0);</script>';
}


///tambah data ke session
if ((isset($_POST['act']) == "tambah") && ($aksi=='tambah')) {

		if ((empty($_SESSION['buatquiz']['setting'])) || ($is == 'awal')) {
			$detail = array(
						"nama" 				=> $_POST['nama'],
						"catatan" 		=> $_POST['catatan'],
						"waktu" 			=> $_POST['waktu'],
			);
			$tujuan = array(
						"mata_kuliah" => $_POST['mata_kuliah'],
						"kelas"				=> $_POST['kelas'],
			);
			$_SESSION['buatquiz']['setting']= array(
					 'detail'    => $detail,
					 'tujuan'    => $tujuan,
			);
			if (empty($is)) {
				$_SESSION['buatquiz']['count'] = 1;
				$_SESSION['buatquiz']['pertanyaan'] = [];
			} else {
				echo '<script>window.setTimeout(function(){window.location.href = "?halaman=quiz&aksi=tambah";}, 500);</script>';
			}
		} else {
			$_SESSION['buatquiz']['pertanyaan'][$_POST['idsoal']] = array(
					 'soal'   => $_POST['soal'],
					 'a'		=> $_POST['a'],
					 'b'    	=> $_POST['b'],
					 'c'    	=> $_POST['c'],
					 'd'    	=> $_POST['d'],
					 'benar' 	=> $_POST['jawaban'],
					 'idsoal' => $_POST['idsoal'],
			);
			if (empty($is) && empty($ids)) {
				$_SESSION['buatquiz']['count'] = $_POST['idsoal']+1;
			}
			if ((isset($_POST['update']))) {
				echo '<script>window.setTimeout(function(){window.location.href = "?halaman=quiz&aksi=tambah";}, 0);</script>';
			}
		}
}

//simpan data dari session ke database
if ((isset($_POST['simpanquiz']) == "simpanquiz") && ($aksi=='tambah')) {
		$detail = array(
			"nama" => $_SESSION['buatquiz']['setting']['detail']['nama'],
			"catatan" => $_SESSION['buatquiz']['setting']['detail']['catatan'],
			"waktu"	=> $_SESSION['buatquiz']['setting']['detail']['waktu'],
			"pertanyaan" => $_SESSION['buatquiz']['pertanyaan'],
		);
		$tujuan = array(
			"mata_kuliah" => $_SESSION['buatquiz']['setting']['tujuan']['mata_kuliah'],
			"kelas"	=> $_SESSION['buatquiz']['setting']['tujuan']['kelas'],
		);
		$wpdb->insert($table_quiz, array(
			'tipe' 		=> $_POST['tipe'],
			'tanggal' => $datenow,
			'iduser' 	=> $user_id,
			'tujuan' 	=> json_encode($tujuan),
			'detail' 	=> json_encode($detail),
		  )
		);
		//hapus session
		unset($_SESSION['buatquiz']);
		echo '<div class="alert alert-success" role="alert">Quiz berhasil disimpan</div>';
		echo '<script>window.setTimeout(function(){
						window.location.href = "?halaman=quiz";
					}, 0);</script>';
}

if ($is == 'awal') {
		$nama 		= $_SESSION['buatquiz']['setting']['detail']['nama'];
		$catatan 	= $_SESSION['buatquiz']['setting']['detail']['catatan'];
		$idmatkul = $_SESSION['buatquiz']['setting']['tujuan']['mata_kuliah'];
		$idkelas 	= $_SESSION['buatquiz']['setting']['tujuan']['kelas'];
		$waktu	 	= $_SESSION['buatquiz']['setting']['detail']['waktu'];
}
if (!empty($ids) && !empty($_SESSION['buatquiz']['pertanyaan'])) {
		$soalx 		= str_replace("\\", '', $_SESSION['buatquiz']['pertanyaan'][$ids]['soal']);
		$soal 		= $soalx;
		$a 				= $_SESSION['buatquiz']['pertanyaan'][$ids]['a'];
		$b 				= $_SESSION['buatquiz']['pertanyaan'][$ids]['b'];
		$c 				= $_SESSION['buatquiz']['pertanyaan'][$ids]['c'];
		$d 				= $_SESSION['buatquiz']['pertanyaan'][$ids]['d'];
		$benar		= $_SESSION['buatquiz']['pertanyaan'][$ids]['benar'];
}



//check jika tidak ada eror
if (empty($sesierrors)) {

?>

<div class="card">
		<div class="card-header bg-info text-white font-weight-bold h6">
			Tambah Quiz
			<?php if (isset($_SESSION['buatquiz'])) { ?>
				<div class="dropdown pull-right">
				  <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    Navigasi
				  </button>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				    <a class="dropdown-item" href="?halaman=quiz&aksi=tambah&is=awal">Pengaturan Dasar</a>
							<?php
							if ((isset($_SESSION['buatquiz']['pertanyaan'])) && (!empty($_SESSION['buatquiz']['pertanyaan']))) {
								$navN = 1;
								foreach ($_SESSION['buatquiz']['pertanyaan'] as $key => $value) {
									echo '<a class="dropdown-item" href="?halaman=quiz&aksi=tambah&ids='.$key.'">Soal '.$navN++.'</a>';
								}
							}
							?>
				  </div>
				</div>
		<?php } ?>
		</div>

		<div class="card-body pb-5">
			<form name="input" method="POST" enctype="multipart/form-data">

				<?php
				if ((!isset($_SESSION['buatquiz'])) || ($is == 'awal')) {
					require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz-template-dasar.php' );
				} else {
					require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz-template-soal.php' );
				}

				echo '<input type="hidden" name="act" value="tambah" />';
				if ($is || $ids) {
					echo '<input type="hidden" name="idsoal" value="'.$ids.'">';
					echo '<input type="hidden" name="update" value="'.$ids.'">';
				} else {
					if ((isset($_SESSION['buatquiz']['count']))) {
						echo '<input type="hidden" name="idsoal" value="'.$_SESSION['buatquiz']['count'].'" />';
					}
				}
				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-4" value="Simpan & Tambah Soal">';
			?>

			</form>
			<?php if (isset($_SESSION['buatquiz'])) { ?>
				<hr>
				<div class="row mt-5">
					<form name="input" method="POST" class="col">
							<input type="hidden" name="reset" value="reset" />
							<input class="btn btn-dark w-100 btn-block" type="submit" value="Reset">
					</form>
					<div class="col">
							<button type="button" class="btn btn-primary w-100 btn-block" data-bs-toggle="modal" data-bs-target="#soalsimpanModal">Simpan</button>
					</div>
				</div>
			<?php } ?>
	</div>
</div>


<?php if (isset($_SESSION['buatquiz'])) { ?>
	<!-- Modal -->
	<div class="modal fade" id="soalsimpanModal" tabindex="-1" role="dialog" aria-labelledby="soalsimpanModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form name="input" method="POST" class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="soalsimpanModalLabel">Simpan Quiz?</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
					<div class="alert alert-warning" role="alert">
						<i class="fa fa-info-circle"></i> Pastikan anda sudah klik "Simpan & Tambah Soal" pada tiap soal
					</div>
					<div class="form-group">
						<select class="form-control" name="tipe">
							<option value="publish">Langsung terbitkan</option>
							<option value="draft">Simpan sebagai konsep</option>
						</select>
						<input type="hidden" name="simpanquiz" value="simpanquiz" />
					</div>
		</div>
		<div class="modal-footer">
			<a class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</a>
			<input type="submit" name="submit" class="btn btn-primary" value="Simpan">
		</div>
		</form>
	</div>
	</div>

	<script>
	jQuery(document).ready(function ($) {
		$(document).on("click",".hapussoal",function(e){
			var get_id = $(this).attr("id");
			$.ajax({
				type: "POST",
				data: "action=hapussoal&idsoal=" + get_id,
				url: sia_ajaxurl,
				success:function(data) {
					window.setTimeout(function(){
						window.location.href = "?halaman=quiz&aksi=tambah";
					}, 500);
				}
			});
		});
	});
	</script>
<?php } ?>

<?php

} else {
	echo $sesierrors;
} ?>
