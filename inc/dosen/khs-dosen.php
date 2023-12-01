<?php global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_krs = $wpdb->prefix . "v_krs";
$table_prodi = $wpdb->prefix . "v_prodi";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$tampil_prodi = $wpdb->get_results("SELECT * FROM $table_prodi");
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$id_prodi = get_user_meta($user_id,'id_prodi',true);
?>

<a class="btn btn-info mb-2" data-bs-toggle="collapse" href=".elform" role="button" aria-expanded="false" aria-controls="elform">
	<i class="fa fa-search"></i> Cari Mata Kuliah
</a>
<div class="elform collapse">
	<form class="border border-info card p-4 mb-3" name="input" method="POST">
		<div class="form-group mb-3">
			<label for="tahun">Tahun Akademik</label>		
			<input required type="number" class="form-control" name="tahun" value="<?php echo date("Y"); ?>" placeholder="Tahun Akademik" />
		</div>
		<div class="form-group mb-3">
			<label for="semester">Semester</label>		
			<input required type="number" class="form-control" name="semester" value="1" placeholder="Semester" />
		</div>
		<div class="form-group mb-3">
			<input type="submit" class="btn btn-info" name="submit" value="Cari">
		</div>
		<input type="hidden" name="pencarian" value="1">
	</form>
</div>


<?php
if (isset($_POST['pencarian']) && isset($_POST['semester']) && isset($_POST['tahun'])){
	$semester = isset($_POST['semester'])? $_POST['semester'] : '' ;
	$tahun = isset($_POST['tahun'])? $_POST['tahun'] : '' ;
	$makulku = "SELECT * FROM $table_makul WHERE id_prodi = $id_prodi AND tahun_akademik = $tahun AND semester = $semester AND id_dosen = $user_id";
} else {
	$makulku = "SELECT * FROM $table_makul WHERE id_prodi = $id_prodi AND id_dosen = $user_id";
}
$tampil_makul = $wpdb->get_results($makulku);

if ($tampil_makul) { ?>
	<div class="table-responsive">
		<table class="table my-4 table-hover table-striped">
			<thead class="thead-dark">
			<tr>
				<th scope="col">Nama Makul</th>
				<th scope="col">Tahun</th>
				<th scope="col">Semester</th>
				<th scope="col">Jenis</th>
				<th scope="col">SKS</th>
				<th scope="col">Dosen</th>
				<th scope="col">Prodi</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($tampil_makul as $hasil) {
			$id_prodi = $hasil->id_prodi;
			$id_dosen = $hasil->id_dosen; ?>
			<tr class="tr-<?php echo $hasil->id_makul; ?>">
				<td><a class="btn btn-info btn-sm d-block" href="<?php the_permalink(); ?>?halaman=tambahkhs&id_makul=<?php echo $hasil->id_makul; ?>" title="KHS <?php echo $hasil->nama_makul; ?>"><?php echo $hasil->nama_makul; ?></a></td>
				<td><?php echo $hasil->tahun_akademik; ?></td>
				<td><?php echo $hasil->semester; ?></td>
				<td><?php echo $hasil->jenis_makul; ?></td>
				<td><?php echo $hasil->sks; ?></td>
				<td><?php echo get_userdata($id_dosen)->first_name; ?></td>
				<?php $show_prodi = $wpdb->get_results("SELECT * FROM $table_prodi WHERE id_prodi =  $id_prodi");
				foreach ($show_prodi as $tampil){ ?>
				<td><?php echo $tampil->nama_prodi; ?></td>
				<?php } ?>
			</tr>
			<?php } //endforeach ?>
			</tbody>
		</table>
	</div>

<?php } else {
	echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Maaf, belum ada data disini.</div>';
} ?>