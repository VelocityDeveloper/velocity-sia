<?php global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$id_makul = isset($_GET['id_makul'])? $_GET['id_makul'] : '' ;
$table_krs = $wpdb->prefix . "v_krs";
$table_khs = $wpdb->prefix . "v_khs";
$table_prodi = $wpdb->prefix . "v_prodi";
$table_jadwal = $wpdb->prefix . "v_jadwal";
$table_mahasiswa = $wpdb->prefix . "v_mahasiswa";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$tampil_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $id_makul");
foreach ($tampil_makul as $makul) {
	$angkatan = $makul->tahun_akademik;
	$semester = $makul->semester;
	$id_prodi = $makul->id_prodi;
	$nama_makul = $makul->nama_makul;
	$jenis_makul = $makul->jenis_makul;
}
$arr_nilai = array(
   '4.00' => 'A --> 4.00',
   '3.00' => 'B --> 3.00-3.99',
   '2.00' => 'C --> 2.00-2.99',
   '1.00' => 'D --> 1.00-1.99',
   '0.00' => 'E --> 0.00-0.99',
);



if(current_user_can('administrator')){ ?>
	<form class="border border-info card p-4 mb-3" name="input" method="POST">
		<div class="form-group mb-3">
			<label class="mb-1" for="id_prodi">Program Studi</label>
			<select name="id_prodi" class="form-control">
			<?php foreach ($tampil_prodi as $prodi) { ?>
				<option value="<?php echo $prodi->id_prodi; ?>"><?php echo $prodi->nama_prodi; ?></option>
			<?php } ?>
			</select>
		</div>
		<div class="form-group mb-3">
			<label class="mb-1" for="tahun">Tahun Akademik</label>		
			<input required type="number" class="form-control" name="tahun" value="<?php echo date("Y"); ?>" placeholder="Tahun Akademik" />
		</div>
		<div class="form-group mb-3">
			<label class="mb-1" for="semester">Semester</label>		
			<input required type="number" class="form-control" name="semester" value="1" placeholder="Semester" />
		</div>
		<div class="form-group mb-3">
			<input type="submit" class="btn btn-info" name="submit" value="Cari Mata Kuliah">
		</div>
		<input type="hidden" name="pencarian" value="1">
	</form>
<?php } ?>

<?php echo '<h5 class="mt-4 elv-judulform">'.$nama_makul.'</h5>'; ?>

<?php if ($jenis_makul == "Pilihan") {
$get_user = $wpdb->get_results("SELECT * FROM $table_krs
JOIN $table_jadwal ON $table_krs.id_jadwal = $table_jadwal.id_jadwal
JOIN $table_makul ON $table_makul.id_makul = $table_jadwal.id_makul
JOIN $table_prodi ON $table_prodi.id_prodi = $table_makul.id_prodi
WHERE $table_makul.id_makul = $id_makul");

} elseif ($jenis_makul == "Wajib") {
$args = array(
	'role'  => 'mahasiswa',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key'     => 'angkatan',
			'value'   => $angkatan,
		),
		array(
			'key'     => 'semester',
			'value'   => $semester,
		),
		array(
			'key'     => 'id_prodi',
			'value'   => $id_prodi,
		),
		array(
			'key'     => 'status',
			'value'   => 'Aktif',
		),
	),
);
$get_user = get_users( $args );
}


if (isset($_POST['update_nilai']) && isset($_POST['submit'])){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	foreach ($get_user as $tampil) {
		
		if ($jenis_makul == "Pilihan") {
			$iduser = $tampil->id_mahasiswa;
		} elseif ($jenis_makul == "Wajib") {
			$iduser = $tampil->ID;
		}
			$nilai_mhs = 'nilai-'.$iduser;
			$id_mhs = 'id_mahasiswa-'.$iduser;
			$edit_nilai_mhs = 'edit_nilai-'.$iduser;
			$id_khs_mhs = 'id_khs-'.$iduser;
			
		if (!empty($_POST[$nilai_mhs]) && ($_POST[$edit_nilai_mhs] == 0) ) {
			$wpdb->insert($table_khs, array(
				'id_makul' => $id_makul,
				'id_mahasiswa' => $_POST[$id_mhs],
				'nilai' => $_POST[$nilai_mhs],
			));
		} 
		if ($_POST[$edit_nilai_mhs] == 1) {
			$wpdb->update( $table_khs, array(
				'id_makul' => $id_makul,
				'id_mahasiswa' => $_POST[$id_mhs],
				'nilai' => $_POST[$nilai_mhs],
			), array('id_khs'=> $_POST[$id_khs_mhs]));			
		}

	}
	echo '<div class="alert alert-success"> Nilai berhasil diupdate.</div>';
	}
} ?>

<form class="form-tambah" name="input" method="POST">

	<div class="table-responsive">
		<table class="table mt-1 mb-4 table-hover table-striped">
			<thead class="thead-dark">
			<tr>
				<th scope="col">NIM</th>
				<th scope="col">Nama Mahasiswa</th>
				<th scope="col">Nilai</th>
			</tr>
			</thead>
			
			<tbody>
			<?php foreach ($get_user as $tampil) {
				if ($jenis_makul == "Pilihan") {
					$idmhs = $tampil->id_mahasiswa;
				} elseif ($jenis_makul == "Wajib") {
					$idmhs = $tampil->ID;
				}
				$get_nilai = $wpdb->get_row( "SELECT * FROM $table_khs WHERE id_mahasiswa = $idmhs AND id_makul = $id_makul" );
				if ($get_nilai) { $nilaiku = $get_nilai->nilai; } ?>
				<tr>
				<td><?php echo get_userdata($idmhs)->user_login; ?></td>
				<td><?php echo get_userdata($idmhs)->first_name; ?></td>
				<td>
				<select class="form-control" name="nilai-<?php echo $idmhs; ?>" style="width: 100%;max-width: 150px;">
					<option value="">-- Nilai --</option>
				<?php if (empty($get_nilai)) { ?>
					<?php foreach ($arr_nilai as $nilai => $ket_nilai) { ?>
					<option value="<?php echo $nilai; ?>"><?php echo $ket_nilai; ?></option>
					<?php } ?>
				<?php } else { ?>
					<?php foreach ($arr_nilai as $nilai => $ket_nilai) { ?>
					<option value="<?php echo $nilai; ?>" <?php if ($nilai == $nilaiku){echo 'selected';};?>><?php echo $ket_nilai; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
				</td>
				</tr>
				<?php if ($get_nilai) { ?>
				<input type="hidden" name="edit_nilai-<?php echo $idmhs; ?>" value="1">
				<input type="hidden" name="id_khs-<?php echo $idmhs; ?>" value="<?php echo $get_nilai->id_khs; ?>">
				<?php } else { ?>
				<input type="hidden" name="edit_nilai-<?php echo $idmhs; ?>" value="0">
				<?php } ?>
				<input type="hidden" name="id_mahasiswa-<?php echo $idmhs; ?>" value="<?php echo $idmhs; ?>">
			<?php } //endforeach ?>
			</tbody>
		</table>
	</div>
<input type="hidden" name="update_nilai" value="1">
<input type="submit" class="btn btn-info" name="submit" value="Submit">
</form>

