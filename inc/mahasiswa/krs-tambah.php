<?php global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_krs = $wpdb->prefix . "v_krs";
$table_jadwal = $wpdb->prefix . "v_jadwal";
$table_prodi = $wpdb->prefix . "v_prodi";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$infouser = get_userdata($user_id);
	$id_semester = get_user_meta($user_id,'semester',true);
	$thn_ajaran = get_user_meta($user_id,'angkatan',true);
	$prodiku = get_user_meta($user_id,'id_prodi',true);
	
$opt_waktuawal = get_option( 'waktuawal')." 00:00:01";
$opt_waktuakhir = get_option( 'waktuakhir')." 23:59:59";
$mulai = strtotime($opt_waktuawal);
$akhir = strtotime($opt_waktuakhir);

$sekarang = date('Y-m-d H:i:s');
$waktusekarang=date('Y-m-d H:i:s', strtotime($sekarang));
$dibuka = date('Y-m-d H:i:s', $mulai);
$ditutup = date('Y-m-d H:i:s', $akhir);


$get_jadwal = $wpdb->get_results("
SELECT * FROM $table_jadwal JOIN $table_makul
ON $table_jadwal.id_makul = $table_makul.id_makul
JOIN $table_krs ON $table_krs.id_jadwal = $table_jadwal.id_jadwal
WHERE $table_makul.tahun_akademik = $thn_ajaran AND $table_makul.semester = $id_semester AND $table_makul.id_prodi = $prodiku AND $table_krs.id_mahasiswa = $user_id");
$totalsks = array();
foreach ($get_jadwal as $jadwal) {$totalsks[] = $jadwal->sks;}
$jmlsksdiambil = array_sum($totalsks);

if (isset($_POST['id_jadwal'])){
	$sks_ini = $_POST['xhjkrd'];
	$dijumlahkan = $sks_ini + $jmlsksdiambil;
	if ($dijumlahkan <= 24) {
		$wpdb->insert($table_krs, array(
			'id_mahasiswa' => $user_id,
			'id_jadwal' => $_POST['id_jadwal'],
			'tahun_akademik' => $thn_ajaran,
			'semester' => $id_semester,
			)
		);
			echo '<div class="alert alert-success">Berhasil ditambahkan.</div>';
	} else {
		echo '<div class="alert alert-danger">Maksimal KRS yang diambil adalah 24 sks.</div><br/>';	
	}
}

$get_jadwal = $wpdb->get_results("
SELECT * FROM $table_jadwal JOIN $table_makul ON $table_jadwal.id_makul = $table_makul.id_makul
WHERE $table_makul.tahun_akademik = $thn_ajaran AND $table_makul.semester = $id_semester AND $table_makul.id_prodi = $prodiku ORDER BY $table_makul.id_makul"); ?>
 
 
<div class="border border-info rounded p-4 my-4 bg-info2">
	<?php if (($waktusekarang > $dibuka) && ($waktusekarang < $ditutup)) { ?>
		<ol class="pl-3 m-0">
			<li>Batas pengisian KRS adalah tanggal <strong><?php echo $opt_waktuawal; ?></strong> sampai <strong><?php echo $opt_waktuakhir; ?></strong>.</li>
			<li>Perubahan KRS tidak dilayani jika batas pengisian KRS telah berakhir.</li>
			<li>Pastikan semua makul wajib (warna hijau) sudah diambil.</li>
			<li>Maksimal KRS yang diambil adalah 24 sks.</li>
			<li>Per mata kuliah hanya bisa mengambil 1 jadwal.</li>
		</ol>
	<?php } else { ?>
		<div class="alert alert-danger">Batas pengisian KRS telah berakhir.</div>
	<?php } ?>
</div>

<a href="?halaman=krs" class="btn btn-info btn-sm text-white"><i class="fa fa-file"></i> Lihat KRS</a>
	


<ul class="event-list">

	<?php foreach ($get_jadwal as $jadwal) {
		$id_jadwal = $jadwal->id_jadwal;
		$id_makul = $jadwal->id_makul;
		$kuota = $jadwal->kuota;
		$jenis_makul = $jadwal->jenis_makul;
		$id_dosen = $jadwal->id_dosen;
		$krs_exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_krs WHERE id_jadwal = $id_jadwal AND id_mahasiswa = $user_id");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_krs WHERE id_jadwal = $id_jadwal");

		$ada_krs = $wpdb->get_results("
		SELECT * FROM $table_krs
		JOIN $table_jadwal ON $table_jadwal.id_jadwal = $table_krs.id_jadwal
		JOIN $table_makul ON $table_jadwal.id_makul = $table_makul.id_makul
		WHERE $table_krs.id_mahasiswa = $user_id AND $table_jadwal.id_makul = $id_makul");
		foreach ($ada_krs as $krs_ku){
			$oki = $krs_ku->id_makul;
		} if (($jenis_makul == "Wajib") && (empty($oki))) {
			$wajib = "wajib-diambil bg-success2";
		} else {
			$wajib = "sudah-diambil";
		} ?>
			
		
		<li class="li-<?php echo $jadwal->id_jadwal; ?> list <?php echo $wajib; ?>">
			<div class="list-box1">
				<div class="align-self-center w-100">
					<h5><?php echo $jadwal->hari; ?></h5>					
					<?php echo $jadwal->jam_kuliah_awal; ?> - <?php echo $jadwal->jam_kuliah_akhir; ?>
				</div>
			</div>
			<div class="list-box2">
				<h5 class="text-info"><?php echo $jadwal->nama_makul; ?></h5>
				<ul class="ul-list">
					<li>Kuota: <strong>(<?php echo $count; ?>/<?php echo $kuota; ?>)</strong></li>
					<li>Dosen: <?php echo get_userdata($jadwal->id_dosen)->first_name; ?></li>
					<li>Kelas: <?php echo $jadwal->kelas; ?></li>
					<li>Ruang: <?php echo $jadwal->ruang; ?></li>
					<li>Tahun Akademik: <?php echo $jadwal->tahun_akademik; ?></li>
					<li>Semester: <?php echo $jadwal->semester; ?></li>
					<li>SKS: <?php echo $jadwal->sks; ?></li>
					<li>Jenis: <?php echo $jadwal->jenis_makul; ?></li>
				</ul>
				<?php if (($waktusekarang > $dibuka) && ($waktusekarang < $ditutup)) { ?>
					<?php if ($krs_exist || (isset($oki) && ($oki == $id_makul))){ ?>
						<span class="absolute-top badge badge-success">Sudah Diambil</span>
					<?php } else {			
						if ($kuota > $count) {	?>
							<form class="form-tambah" name="input" method="POST"> 
							<input type="hidden" name="id_jadwal" value="<?php echo $jadwal->id_jadwal; ?>">
							<input type="hidden" name="xhjkrd" value="<?php echo $jadwal->sks; ?>">
							<button class="absolute-top btn btn-info btn-sm text-white">Ambil</button>	
							</form>
						<?php } else { ?>
							<span class="absolute-top badge badge-warning">Kuota Penuh</span>
						<?php } ?>
					<?php } ?>
				<?php } ?>
					
			</div>
				
		</li>
		
	<?php } ?>
	
</ul>
