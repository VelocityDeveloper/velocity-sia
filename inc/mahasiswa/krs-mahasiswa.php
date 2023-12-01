<?php global $wpdb;
global $velocityoption;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_krs = $wpdb->prefix . "v_krs";
$table_jadwal = $wpdb->prefix . "v_jadwal";
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

if (isset($_POST['hapus_ini'])) {
	$wpdb->delete($table_krs, array('id_jadwal' => $_POST['hapus_ini'],));
	echo '<div class="alert alert-success">Berhasil dihapus.</div>';
}

$get_jadwal = $wpdb->get_results("
SELECT * FROM $table_jadwal JOIN $table_makul
ON $table_jadwal.id_makul = $table_makul.id_makul
JOIN $table_krs ON $table_krs.id_jadwal = $table_jadwal.id_jadwal
WHERE $table_makul.tahun_akademik = $thn_ajaran AND $table_makul.semester = $id_semester AND $table_makul.id_prodi = $prodiku AND $table_krs.id_mahasiswa = $user_id"); ?>


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
 

<?php if (($waktusekarang > $dibuka) && ($waktusekarang < $ditutup)) { ?>
<a href="?halaman=krstambah" class="btn btn-info btn-sm text-white"><i class="fa fa-plus-circle"></i> Tambah KRS</a> 
<?php } ?>
<div onclick="printArea('#event-list')" class="klikprint btn btn-info btn-sm text-white"><i class="fa fa-print"></i> Print</div>

<div id="event-list">
<ul class="event-list">
<?php $totalsks = array();
	foreach ($get_jadwal as $jadwal) { //Untuk menampilkan jadwal Pilihan 
	$id_jadwal = $jadwal->id_jadwal;
	$id_dosen = $jadwal->id_dosen;
	$krs_exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_krs WHERE id_jadwal = $id_jadwal");
	if ($krs_exist) { // Jika KRS ini ada
		$totalsks[] = $jadwal->sks; ?>	

		<li class="li-<?php echo $jadwal->id_jadwal; ?> list position-relative">
			<div class="list-box1">
				<div class="align-self-center w-100">
					<h5><?php echo $jadwal->hari; ?></h5>					
					<?php echo $jadwal->jam_kuliah_awal; ?> - <?php echo $jadwal->jam_kuliah_akhir; ?>
				</div>
			</div>
			<div class="list-box2">
				<h5 class="text-info"><?php echo $jadwal->nama_makul; ?></h5>
				<ul class="ul-list">
					<li>Dosen: <?php echo get_userdata($jadwal->id_dosen)->first_name; ?></li>
					<li>Kelas: <?php echo $jadwal->kelas; ?></li>
					<li>Ruang: <?php echo $jadwal->ruang; ?></li>
					<li>Tahun Akademik: <?php echo $jadwal->tahun_akademik; ?></li>
					<li>Semester: <?php echo $jadwal->semester; ?></li>
					<li>SKS: <?php echo $jadwal->sks; ?></li>
					<li>Jenis: <?php echo $jadwal->jenis_makul; ?></li>
				</ul>	
			</div>
			<?php if (($waktusekarang > $dibuka) && ($waktusekarang < $ditutup)) { ?>
				<a class="absolute-top text-secondary" data-bs-toggle="collapse" href="#trd-<?php echo $id_jadwal; ?>" role="button" aria-expanded="false" aria-controls="trd-<?php echo $id_jadwal; ?>"><i class="fa fa-ellipsis-h"></i></a>		
				<div id="trd-<?php echo $id_jadwal; ?>" class="collapse absolute-bottom">
					<form method="post">
						<input name="hapus_ini" type="hidden" value="<?php echo $jadwal->id_jadwal; ?>" />
						<button class="btn btn-danger"><i class="fa fa-trash text-white"></i></button>	
					</form>
				</div>
			<?php } ?>				
		</li>
		
	<?php } // endif
	} // endforeach ?>
</ul>

<?php $jmlsksdiambil = array_sum($totalsks); ?>
<div class="alert alert-warning">TOTAL SKS YANG DIAMBIL: <strong><?php echo $jmlsksdiambil; ?></strong></div>	
</div>