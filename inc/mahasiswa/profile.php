<?php
$current_id = get_current_user_id();
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';

if (($aksi=='edit') && (get_option('profil_mahasiswa')=='ya')) {
	echo '<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">
			<span class="float-left mt-sm-2">Edit Profil</span> 
			<a class="btn btn-light btn-sm float-right" href="?halaman=profil&aksi=editakun"><i class="fa fa-pencil"></i> Edit Akun</a>
		</div>
		<div class="card-body pb-5">';
		elv_edituser($profil_mahasiswa,$current_id);
	echo '</div></div>';
} if (($aksi=='editakun') && (get_option('akun_profil_mahasiswa')=='ya')){
	echo '<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">
			<span class="float-left mt-sm-2">Edit Akun</span> 
			<a class="btn btn-light btn-sm float-right" href="?halaman=profil&aksi=edit"><i class="fa fa-pencil"></i> Edit Profil</a>
		</div>
		<div class="card-body pb-5">';
		elv_edit_akun_user($profil_mahasiswa,$current_id);
	echo '</div></div>';	
} if(empty($aksi)) {
	elv_lihatuser($profil_mahasiswa,$current_id);
	if (get_option('profil_mahasiswa')=='ya'){
		echo '<a class="btn btn-info btn-sm me-1" href="?halaman=profil&aksi=edit"><i class="fa fa-pencil"></i> Edit Profil</a>';
	} if (get_option('akun_profil_mahasiswa')=='ya'){
		echo '<a class="btn btn-info btn-sm" href="?halaman=profil&aksi=editakun"><i class="fa fa-pencil"></i> Edit Akun</a>';
	}
}	

?>