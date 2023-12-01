<?php
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id = isset($_GET['id'])? $_GET['id'] : '' ;
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_jadwal = $wpdb->prefix . "v_jadwal";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$table_Kelas = $wpdb->prefix . "v_kelas";
$table_ruang = $wpdb->prefix . "v_ruang";

$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul");
$tampil_kelas = $wpdb->get_results("SELECT * FROM $table_Kelas");
$tampil_ruang = $wpdb->get_results("SELECT * FROM $table_ruang");
$arr_hari = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');


///tambah data
if (isset($_POST['tambah']) && isset($_POST['ruang'])){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$wpdb->insert($table_jadwal, array(
		'hari' => $_POST['hari'],
		'jam_kuliah_awal' => $_POST['jam_kuliah_awal'],
		'jam_kuliah_akhir' => $_POST['jam_kuliah_akhir'],
		'kelas' => $_POST['kelas'],
		'ruang' => $_POST['ruang'],
		'kuota' => $_POST['kuota'],
		'id_makul' => $_POST['id_makul'],
		)
	);
	echo '<div class="alert alert-success">Berhasil: Jadwal berhasil ditambahkan.</div>';
	}
}

///edit data
if (isset($_POST['edit']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$wpdb->update( $table_jadwal, array(
		'hari' => $_POST['hari'],
		'jam_kuliah_awal' => $_POST['jam_kuliah_awal'],
		'jam_kuliah_akhir' => $_POST['jam_kuliah_akhir'],
		'kelas' => $_POST['kelas'],
		'ruang' => $_POST['ruang'],
		'kuota' => $_POST['kuota'],
		'id_makul' => $_POST['id_makul'],
	), array('id_jadwal'=> $id));
	echo '<div class="alert alert-success">Berhasil: Jadwal berhasil diupdate.</div>';
	}
}

if (!empty($id) && ($aksi=='edit')) {
	$tampil_jadwal = $wpdb->get_results("SELECT * FROM $table_jadwal WHERE id_jadwal = $id");
	$data = $tampil_jadwal[0];
	$id_jadwal			= $data->id_jadwal;
	$hari 				= $data->hari;
	$jam_kuliah_awal 	= $data->jam_kuliah_awal;
	$jam_kuliah_akhir 	= $data->jam_kuliah_akhir;
	$idkelas 			= $data->kelas;
	$idruang 			= $data->ruang;
	$kuota 				= $data->kuota;
	$id_makul 			= $data->id_makul;
}

 ?>
	
<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">		
		<?php if($aksi=='tambah') { 
			echo 'Tambah Jadwal';
		} else if ($aksi=='edit') {				
			echo 'Edit Jadwal';
		}
		?>
		</div>
		
		<div class="card-body pb-5">
			<form name="input" method="POST">
			<div class="form-group mb-3">
				<label class="mb-1" for="hari" class="font-weight-bold">Hari</label>
				<select name="hari" class="form-control">
					<?php foreach ($arr_hari as $Xhari) { ?>
						<option value="<?php echo $Xhari; ?>" <?php if ((isset($hari))&&($Xhari == $hari)){echo 'selected';};?>><?php echo $Xhari; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="jam_kuliah_awal" class="font-weight-bold">Jam Kuliah</label>
				<div class="row">
				  <div class="col-6 pe-1">
					<input required type="text" class="form-control" name="jam_kuliah_awal" value="<?php if (isset($jam_kuliah_awal)) { echo $jam_kuliah_awal; }?>" id="awal" placeholder="Jam Mulai" />
				  </div>
				  <div class="col-6 ps-1">
					<input required type="text" class="form-control" name="jam_kuliah_akhir" value="<?php if (isset($jam_kuliah_akhir)) { echo $jam_kuliah_akhir; }?>" id="akhir" placeholder="Jam Akhir" />
				  </div>
				</div>
				<small class="text-primary">contoh: 22:30</small>
			</div>			
			<div class="form-group mb-3">
				<label class="mb-1" for="kelas" class="font-weight-bold">Nama Kelas</label>
				<select class="form-control" name="kelas">
					<option value="">Pilih Kelas</option>
					<?php foreach ( $tampil_kelas as $kelas ) { ?>	
						<option value="<?php echo $kelas->nama; ?>" <?php if ((isset($idkelas))&&($kelas->nama == $idkelas)){echo 'selected';};?>><?php echo $kelas->nama; ?></option>
					<?php } ?>
				</select>
			</div>						
			<div class="form-group mb-3">
				<label class="mb-1" for="ruang" class="font-weight-bold">Ruang Kelas</label>
				<select class="form-control" name="ruang">
					<option value="">Pilih Ruang</option>
					<?php foreach ( $tampil_ruang as $ruang ) { ?>	
						<option value="<?php echo $ruang->nama; ?>" <?php if ((isset($idruang))&&($ruang->nama == $idruang)){echo 'selected';};?>><?php echo $ruang->nama; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="kuota" class="font-weight-bold">Kuota</label>
				<input required type="number" class="form-control" name="kuota" value="<?php if (isset($kuota)) { echo $kuota; }?>" placeholder="Kuota" />
			</div>
			<div class="form-group mb-3">
				<label class="mb-1" for="id_makul" class="font-weight-bold">Matakuliah</label>
				<select id="pilihmakul" class="form-control" name="id_makul">
					<option value="">Pilih Mata Kuliah</option>
					<?php foreach ( $tampil_makul as $makul ) { ?>	
						<option value="<?php echo $makul->id_makul; ?>" <?php if ((isset($id_makul))&&($makul->id_makul == $id_makul)){echo 'selected';};?>><?php echo $makul->nama_makul; ?></option>
					<?php } ?>
				</select>
			</div>
			<div id="dbinfo">			
				<?php 
				if (isset($id_makul)) { 
				$ijfddik = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $id_makul");
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
				?>			
			</div>
			
			<?php if($aksi=='tambah') { 
				echo '<input type="hidden" name="tambah" value="ya" />';
				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Tambah Jadwal">';
			} else if ($aksi=='edit') {				
				echo '<input type="hidden" name="edit" value="ya" />';
				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-5" value="Update Jadwal">';
			}
			?>
			
			
			</form>
	</div>
</div>




<script>
jQuery(document).ready(function ($) {
	$('#awal, #akhir').datetimepicker({
		datepicker:false,
		format:'H:i',
		formatTime:'H:i',
		step:5
	});
	$("#pilihmakul").change(function() {
	$("#dbinfo").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
	var get_id = $(this).val();
		$.ajax({  
		type: "POST",  
		data: "action=pilihmakul&id=" + get_id, 
		url: sia_ajaxurl,
			success: function(data) {
				//  Code to be executed if the request succeeds eg:
				$("#dbinfo").html(data);
				//  The data variable contains the content returned from the server
				},
			error: function() {
				//  Code to be executed if the request fails
				}
		});
	});
});
</script>
