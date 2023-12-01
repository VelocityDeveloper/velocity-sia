<?php global $wpdb;
global $velocityoption;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_khs = $wpdb->prefix . "v_khs";
$table_jadwal = $wpdb->prefix . "v_jadwal";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$tampil = isset($_GET['tampil'])? $_GET['tampil'] : '';
$semester = isset($_GET['semester'])? $_GET['semester'] : '';
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$infouser = get_userdata($user_id);
	$id_semester = get_user_meta($user_id,'semester',true);
	$tahun_akademik = get_user_meta($user_id,'angkatan',true);
	$prodiku = get_user_meta($user_id,'id_prodi',true);

$semester_query = $wpdb->get_results("SELECT * FROM $table_khs JOIN $table_makul
ON $table_khs.id_makul = $table_makul.id_makul
WHERE $table_khs.id_mahasiswa = $user_id GROUP BY $table_makul.semester");

if (isset($_GET['semester'])){
	$semesterku = esc_html($_GET['semester']);	
} else {
	$semesterku = $id_semester;
}

$get_nilai = $wpdb->get_results("SELECT * FROM $table_khs JOIN $table_makul
ON $table_khs.id_makul = $table_makul.id_makul
WHERE $table_khs.id_mahasiswa = $user_id AND $table_makul.tahun_akademik = $tahun_akademik AND $table_makul.semester = $semesterku"); ?>

<ul class="nav nav-pills">
	<?php foreach ($semester_query as $tabel) {
		$class = $tabel->semester == $id_semester && empty($_GET['tampil']) ? ' active' : ' border'; ?>
		<li class="nav-item me-2 mb-2">
			<a class="nav-link<?php echo $class;?>" href="<?php echo the_permalink(); ?>?halaman=nilai&semester=<?php echo $tabel->semester; ?>">Semester <?php echo $tabel->semester; ?></a>
		</li>
	<?php } ?>
	<?php $class_all = $_GET['tampil'] == 'semua' ? ' active' : ' border'; ?>
	<li class="nav-item me-2 mb-2">
		<a class="nav-link<?php echo $class_all;?>" href="<?php echo the_permalink(); ?>?halaman=nilai&tampil=semua">Semua Semester</a>
	</li>
</ul>

<div class="mt-4 mb-3">
	<div onclick="printArea('.frame-khs')" class="klikprint btn btn-info btn-sm text-white">
		<i class="fa fa-print"></i> Print
	</div>
</div>


<div class="frame-khs">
<?php if($tampil=='') {
	
echo '<h5 class="elv-judulform">Semester '.$semesterku.'</h5>'; ?>

	<div class="table-responsive">
	<table class="table mb-4 table-bordered bg-white">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Nama Matakuliah</th>
			<th scope="col">Tahun Akademik</th>
			<th scope="col">Semester</th>
			<th scope="col">SKS</th>
			<th scope="col">Grade</th>
			<th scope="col">Bobot</th>
			<th scope="col">Nilai</th>
		</tr>		
		</thead>
		<tbody>
		<?php $jml_sks = array();
		$jml_nilai = array();
		foreach ($get_nilai as $nilai) {
		$value = $nilai->nilai;
		$sks = $nilai->sks;
		$nilai_ini =  $value * $sks;
			$jml_sks[] = $sks;
			$jml_nilai[] = $nilai_ini; ?>
		<tr>
			<td><?php echo $nilai->nama_makul; ?></td>
			<td><?php echo $nilai->tahun_akademik; ?></td>
			<td><?php echo $nilai->semester; ?></td>
			<td><?php echo $sks; ?></td>
			<td><?php if ($value == "0.00") {
				echo "E";
			} if ($value == "1.00") {
				echo "D";
			} if ($value == "2.00") {
				echo "C";
			} if ($value == "3.00") {
				echo "B";
			} if ($value == "4.00") {
				echo "A";
			} ?>
			</td>
			<td><?php echo $nilai->nilai; ?></td>
			<td><?php echo $nilai_ini; ?></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>
	</div>

	<?php if ($get_nilai) {
	$total_sks = array_sum($jml_sks);
	$total_nilai = array_sum($jml_nilai); ?>
	TOTAL SKS KESELURUHAN: <strong><?php echo $total_sks; ?></strong><br/>
	TOTAL NILAI KESELURUHAN: <strong><?php echo $total_nilai; ?></strong><br/>
	<?php $ipk = $total_nilai / $total_sks; ?>
	IPK ANDA: <strong><?php echo number_format($ipk,2); ?></strong><br/>
	<?php } ?>



<?php } if($tampil=='semua') { ?>
	<div class="table-responsive">
	<table class="table mb-4 table-bordered bg-white">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Nama Matakuliah</th>
			<th scope="col">Tahun Akademik</th>
			<th scope="col">Semester</th>
			<th scope="col">SKS</th>
			<th scope="col">Grade</th>
			<th scope="col">Bobot</th>
			<th scope="col">Nilai</th>
		</tr>
		</thead>
		<tbody>
		<?php $semua_sks = array();
		$semua_nilai = array();
		foreach ($semester_query as $tabel) {
		$semester = $tabel->semester;
		$hahaha = $wpdb->get_results("SELECT * FROM $table_khs JOIN $table_makul
		ON $table_khs.id_makul = $table_makul.id_makul
		WHERE $table_khs.id_mahasiswa = $user_id AND $table_makul.tahun_akademik = $tahun_akademik AND $table_makul.semester = $semester"); ?>
		<?php $jml_sks = array();
		$jml_nilai = array();
		foreach ($hahaha as $nilai) {
		$value = $nilai->nilai;
		$sks = $nilai->sks;
		$nilai_ini =  $value * $sks;
			$jml_sks[] = $sks;
			$jml_nilai[] = $nilai_ini; ?>
		<tr>
			<td><?php echo $nilai->nama_makul; ?></td>
			<td><?php echo $nilai->tahun_akademik; ?></td>
			<td><?php echo $nilai->semester; ?></td>
			<td><?php echo $sks; ?></td>
			<td><?php if ($value == "0.00") {
				echo "E";
			} if ($value == "1.00") {
				echo "D";
			} if ($value == "2.00") {
				echo "C";
			} if ($value == "3.00") {
				echo "B";
			} if ($value == "4.00") {
				echo "A";
			} ?>
			</td>
			<td><?php echo $nilai->nilai; ?></td>
			<td><?php echo $nilai_ini; ?></td>
		</tr>
		<?php }
			$total_sks = array_sum($jml_sks);
			$total_nilai = array_sum($jml_nilai);
			$jml_semua_sks[] = $total_sks;
			$jml_semua_nilai[] = $total_nilai;
		} ?>
		</tbody>
	</table>
	</div>

	<?php if ($semester_query) {
		$total_semua_sks = array_sum($jml_semua_sks);
		$total_semua_nilai = array_sum($jml_semua_nilai); ?>
		TOTAL SKS KESELURUHAN: <strong><?php echo $total_semua_sks; ?></strong><br/>
		TOTAL NILAI KESELURUHAN: <strong><?php echo $total_semua_nilai; ?></strong><br/>
		<?php $ipk = $total_semua_nilai / $total_semua_sks; ?>
		IPK ANDA: <strong><?php echo number_format($ipk,2); ?></strong><br/>
	<?php } ?>

<?php } ?>
</div>

