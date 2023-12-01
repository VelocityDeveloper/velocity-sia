<?php $argsku = array(
	'role'         => 'mahasiswa',
	'meta_key'     => 'status',
	'meta_value'   => 'Pending',
 );
$getpnding = get_users( $argsku );
$jmlmhs = 0;
foreach ($getpnding as $user_m) {$jmlmhs++;}
$get_stts = isset($_GET['status'])? $_GET['status'] : '';
$user_id = get_current_user_id();

if(current_user_can('administrator')){
$halaman = isset($_GET['halaman'])? $_GET['halaman'] : '';

echo '<div id="elv-main" class="row">';
	//menu
	echo '<div id="elv-sidebar" class="col-md-3"><div class="card pt-sm-4 pb-sm-5">';

		echo '<div class="elv-profil-box text-center">';
			echo '<div class="mb-sm-2">'.elv_fotouser($user_id).'</div>';
			echo '<h5 class="elv-nama mt-2 mb-5" >'.elv_nama($user_id).'</h5>';
			echo '<button class="elv-menu-button" type="button" data-bs-toggle="collapse" data-bs-target="#elv-menunav" aria-controls="elv-menunav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="fa fa-bars"></span></button>';
		echo '</div>';

		$array_menu = array (
			'halaman_sekarang' => $halaman,
			'menu' => array (
				array('judul' => 'Umum', 'halaman' => '#',
					'submenu' => array (
						array('judul' => 'Kelas', 'halaman' => 'kelas'),
						array('judul' => 'Ruang', 'halaman' => 'ruang'),
						array('judul' => 'Fakultas', 'halaman' => 'fakultas'),
						array('judul' => 'Program Studi', 'halaman' => 'prodi'),
					),
				),
				array('judul' => 'Dosen', 'halaman' => 'dosen'),
				array('judul' => 'Mahasiswa', 'halaman' => 'mahasiswa', 'notif' => $jmlmhs),
				array('judul' => 'Mata Kuliah', 'halaman' => 'makul'),
				array('judul' => 'Jadwal Kuliah', 'halaman' => 'jadwal'),
				array('judul' => 'Jadwal KRS', 'halaman' => 'jadwalkrs'),
				array('judul' => 'KHS', 'halaman' => 'khs'),
				array('judul' => 'Tugas', 'halaman' => 'tugas'),
				array('judul' => 'Materi', 'halaman' => 'materi'),
				array('judul' => 'Quiz', 'halaman' => 'quiz'),
				array('judul' => 'Pengaturan', 'halaman' => 'pengaturan'),
			),
		);
		echo '<div id="elv-menunav" class="collapse">'.elv_menu($array_menu).'</div>';
	echo '</div></div>';

	echo '<div id="elv-contain" class="col-md-9">';
		$class = 'elv-judulpage card py-2 px-3 mb-4';

		 if($halaman=='fakultas' || empty($halaman)) {
			echo '<h5 class="'.$class.'"> Fakultas </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/fakultas.php' );
		} if($halaman=='prodi') {
			echo '<h5 class="'.$class.'"> Program Studi </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/prodi.php' );
		} if($halaman=='kelas') {
			echo '<h5 class="'.$class.'"> Kelas </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/kelas.php' );
		} if($halaman=='ruang') {
			echo '<h5 class="'.$class.'"> Ruang </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/ruang.php' );
		} if($halaman=='makul') {
			echo '<h5 class="'.$class.'"> Mata Kuliah </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/mata-kuliah.php' );
		} if($halaman=='jadwal') {
			echo '<h5 class="'.$class.'"> Jadwal </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/jadwal.php' );
		} if($halaman=='jadwalkrs') {
			echo '<h5 class="'.$class.'"> Jadwal KRS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/jadwal-krs.php' );
		} if($halaman=='dosen') {
			echo '<h5 class="'.$class.'"> Dosen </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/dosen.php' );
		}  if($halaman=='mahasiswa') {
			echo '<h5 class="'.$class.'"> Mahasiswa </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/mahasiswa.php' );
		} if($halaman=='khs') {
			echo '<h5 class="'.$class.'"> KHS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/khs.php' );
		} if($halaman=='tugas') {
			echo '<h5 class="'.$class.'"> Tugas </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/tugas/tugas.php' );
		} if($halaman=='materi') {
			echo '<h5 class="'.$class.'"> Materi </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/materi/materi.php' );
		} if($halaman=='quiz') {
			echo '<h5 class="'.$class.'"> Quiz </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz.php' );
		} if($halaman=='tambahkhs') {
			echo '<h5 class="'.$class.'"> Tambah KHS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/khs-tambah.php' );
		} if($halaman=='pengaturan') {
			echo '<h5 class="'.$class.'"> Pengaturan </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/pengaturan.php' );
		} if($halaman=='editfoto') {
			echo '<h5 class="'.$class.'"> Edit Foto </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/edit-foto.php' );
		}
	echo '</div>';
echo '</div>';
} ?>
