<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$user_id = get_current_user_id();

if(current_user_can('mahasiswa')){
$halaman = isset($_GET['halaman'])? $_GET['halaman'] : '';

echo '<div id="elv-main" class="row">';
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
				array('judul' => 'Profil', 'halaman' => 'profil'),
				array('judul' => 'Tugas', 'halaman' => 'tugas'),
				array('judul' => 'Materi', 'halaman' => 'materi'),
				array('judul' => 'KRS', 'halaman' => 'krs'),
				array('judul' => 'KHS', 'halaman' => 'nilai'),
				array('judul' => 'Quiz', 'halaman' => 'quiz'),
			),
		);
		echo '<div id="elv-menunav" class="collapse">'.elv_menu($array_menu).'</div>';
	echo '</div></div>';

	echo '<div id="elv-contain" class="col-md-9">';
		$class = 'elv-judulpage card py-2 px-3 mb-4';

		if(empty($halaman)) {
			echo '<h5 class="'.$class.'"> Beranda </h5>';
			elv_lihatuser($profil_mahasiswa,$user_id);
		} if($halaman=='profil') {
			echo '<h5 class="'.$class.'"> Profil </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/mahasiswa/profile.php' );
		} if($halaman=='krs') {
			echo '<h5 class="'.$class.'"> KRS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/mahasiswa/krs-mahasiswa.php' );
		} if($halaman=='krstambah') {
			echo '<h5 class="'.$class.'"> KRS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/mahasiswa/krs-tambah.php' );
		} if($halaman=='nilai') {
			echo '<h5 class="'.$class.'"> KHS </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/mahasiswa/nilai.php' );
		} if($halaman=='tugas') {
			echo '<h5 class="'.$class.'"> Tugas </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/tugas/tugas-siswa.php' );
		} if($halaman=='materi') {
			echo '<h5 class="'.$class.'"> Materi </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/materi/materi-siswa.php' );
		} if($halaman=='quiz') {
			echo '<h5 class="'.$class.'"> Quiz </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz-siswa.php' );
		} if($halaman=='editfoto') {
			echo '<h5 class="'.$class.'"> Edit Foto </h5>';
			require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/edit-foto.php' );
		}
	echo '</div>';
echo '</div>';
} ?>
