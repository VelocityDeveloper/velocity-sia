<?php
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$table_prodi = $wpdb->prefix . "v_prodi";
$tampil_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
$arr_jenis = array('Wajib', 'Pilihan');
$args = array(
	'role' => 'dosen',
); 
$get_dosen = get_users( $args );

if($aksi=='tambah') {

	if (isset($_POST['tahun_akademik']) && isset($_POST['semester']) && isset($_POST['tambahmatakuliah'])){
		if (!(wp_get_current_user()->user_login == "admindemo")){
		$wpdb->insert($table_makul, array(
			'nama_makul' => $_POST['nama_makul'],
			'tahun_akademik' => $_POST['tahun_akademik'],
			'semester' => $_POST['semester'],
			'jenis_makul' => $_POST['jenis_makul'],
			'sks' => $_POST['sks'],
			'id_prodi' => $_POST['id_prodi'],
			'id_dosen' => $_POST['id_dosen'],
			)
		);
		echo '<div class="alert alert-success">Berhasil: Mata Kuliah <strong>'.$_POST['nama_makul'].'</strong> berhasil ditambahkan.</div>';
		}
	} ?>

	<h6 class="elv-judulform d-block"><strong>Tambah Mata Kuliah</strong></h6> 

	<form name="input" method="POST">
		<div class="form-group row mb-3">
			<label for="nama_makul" class="col-sm-3 col-form-label">Nama Mata Kuliah</label>
			<div class="col-sm-9">
				<input required class="form-control" type="text" name="nama_makul" value="" />
			</div>
		</div>
		<div class="form-group row mb-3">
			<label for="tahun_akademik" class="col-sm-3 col-form-label">Tahun Akademik</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="tahun_akademik" value="" />
			</div>
		</div>		
		<div class="form-group row mb-3">
			<label for="semester" class="col-sm-3 col-form-label">Semester</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="semester" value="" />
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="jenis_makul" class="col-sm-3 col-form-label">Jenis Mata Kuliah</label>
			<div class="col-sm-9">
				<select name="jenis_makul" class="form-control">
					<?php foreach ($arr_jenis as $jenis) { ?>
						<option value="<?php echo $jenis; ?>"><?php echo $jenis; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="sks" class="col-sm-3 col-form-label">SKS</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="sks" value="" />
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="id_dosen" class="col-sm-3 col-form-label">Dosen</label>
			<div class="col-sm-9">
				<select name="id_dosen" class="form-control">
					<?php foreach ( $get_dosen as $dosen ) { ?>	
						<option value="<?php echo $dosen->ID; ?>"><?php echo $dosen->first_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row mb-3">
			<label for="id_prodi" class="col-sm-3 col-form-label">Program Studi</label>
			<div class="col-sm-9">
				<select name="id_prodi" class="form-control">
					<?php foreach ( $tampil_prodi as $prodi ) { ?>	
						<option value="<?php echo $prodi->id_prodi; ?>"><?php echo $prodi->nama_prodi; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group mt-5">			
			<input type="submit" name="submit" class="btn btn-info" value="Tambah">
		</div>
		<input type="hidden" name="tambahmatakuliah" value="1">
	</form>	


<?php } elseif($aksi=='edit') {
		
	$id_makul = isset($_GET['id_makul'])? $_GET['id_makul'] : '' ;

	if (isset($_POST['editmatakuliah']) && isset($_POST['semester'])){
	if (!(wp_get_current_user()->user_login == "admindemo")){
		$wpdb->update( $table_makul, array(
			'nama_makul' => $_POST['nama_makul'],
			'tahun_akademik' => $_POST['tahun_akademik'],
			'semester' => $_POST['semester'],
			'jenis_makul' => $_POST['jenis_makul'],
			'sks' => $_POST['sks'],
			'id_prodi' => $_POST['id_prodi'],
			'id_dosen' => $_POST['id_dosen'],
		), array('id_makul'=> $id_makul));
		echo '<div class="alert alert-success">Berhasil: Mata Kuliah <strong>'.$_POST['nama_makul'].'</strong> berhasil diupdate.</div>';
	}
	}

	$tampil_matakuliah = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $id_makul");
	foreach ($tampil_matakuliah as $makul) {	?>	

	<h6 class="elv-judulform d-block"><strong>Edit Mata Kuliah "<?php echo $makul->nama_makul; ?>"</strong></h6> 

	<form class="form-tambah" name="input" method="POST">
		<div class="form-group row mb-3">
			<label for="nama_makul" class="col-sm-3 col-form-label">Nama Mata Kuliah</label>
			<div class="col-sm-9">
				<input required class="form-control" type="text" name="nama_makul" value="<?php echo $makul->nama_makul; ?>" />
			</div>
		</div>
		<div class="form-group row mb-3">
			<label for="tahun_akademik" class="col-sm-3 col-form-label">Tahun Akademik</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="tahun_akademik" value="<?php echo $makul->tahun_akademik; ?>" />
			</div>
		</div>		
		<div class="form-group row mb-3">
			<label for="semester" class="col-sm-3 col-form-label">Semester</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="semester" value="<?php echo $makul->semester; ?>" />
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="jenis_makul" class="col-sm-3 col-form-label">Jenis Mata Kuliah</label>
			<div class="col-sm-9">
				<select name="jenis_makul" class="form-control">
					<?php foreach ($arr_jenis as $jenis) { ?>
						<option value="<?php echo $jenis; ?>" <?php if ($jenis == $makul->jenis_makul){echo 'selected';};?>><?php echo $jenis; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="sks" class="col-sm-3 col-form-label">SKS</label>
			<div class="col-sm-9">
				<input required class="form-control" type="number" name="sks" value="<?php echo $makul->sks; ?>" />
			</div>
		</div>			
		<div class="form-group row mb-3">
			<label for="id_dosen" class="col-sm-3 col-form-label">Dosen</label>
			<div class="col-sm-9">
				<select name="id_dosen" class="form-control">
					<?php $userid_dosen = $makul->id_dosen;
					foreach ($get_dosen as $tampil){
					$idku = $tampil->ID; ?>
						<option value="<?php echo $idku; ?>" <?php if ($userid_dosen == $idku){echo 'selected';};?>><?php echo $tampil->first_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row mb-3">
			<label for="id_prodi" class="col-sm-3 col-form-label">Program Studi</label>
			<div class="col-sm-9">
				<select name="id_prodi" class="form-control">
					<?php $userid_prodi = $makul->id_prodi;
					$show_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
					foreach ($show_prodi as $tampil){
					$idku = $tampil->id_prodi; ?>
							<option value="<?php echo $idku; ?>" <?php if ($userid_prodi == $idku){echo 'selected';};?>><?php echo $tampil->nama_prodi; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group mt-5">	
			<input type="hidden" name="editmatakuliah" value="1">		
			<input type="submit" name="submit" class="btn btn-info" value="update">
		</div>
	</form>

	<?php } //endforeach


 } //endif halaman ?>