<?php
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id = isset($_GET['id'])? $_GET['id'] : '';
$stts = isset($_GET['status'])? $_GET['status'] : '';
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_prodi = $wpdb->prefix . "v_prodi";

if($aksi=='tambah') {
	echo '<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">Tambah Mahasiswa</div>
		<div class="card-body pb-5">';
		elv_tambahuser($profil_mahasiswa);
	echo '</div></div>';
} else if($aksi=='edit') {
	echo '<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">
			<span class="float-left mt-sm-2">Edit Profil</span> 
			<a class="btn btn-light btn-sm float-right" href="?halaman=mahasiswa&aksi=editakun&id='.$id.'"><i class="fa fa-pencil"></i> Edit Akun</a>
		</div>
		<div class="card-body pb-5">';
		elv_edituser($profil_mahasiswa,$id);
	echo '</div></div>';
} else if($aksi=='editakun') {
	echo '<div class="card">
		<div class="card-header bg-info text-white font-weight-bold">
			<span class="float-left mt-sm-2">Edit Akun</span> 
			<a class="btn btn-light btn-sm float-right" href="?halaman=mahasiswa&aksi=edit&id='.$id.'"><i class="fa fa-pencil"></i> Edit Profil</a>
		</div>
		<div class="card-body pb-5">';
		elv_edit_akun_user($profil_mahasiswa,$id);
	echo '</div></div>';
} else {

	if (($stts == 'aktif') || empty($stts)) {
		$status = "Aktif";
	} else {
		$status = "Tidak Aktif";
	}
	
	$items_per_page = 25;
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
	$offset = ( $page * $items_per_page ) - $items_per_page;
	$args = array(
		'role'         => 'mahasiswa',
		'meta_key'     => 'status',
		'meta_value'   => $status,
		'number'   => $items_per_page,
		'offset'   => $offset,
	 ); 
	$get_users = get_users( $args ); ?>

	<a class="btn btn-info mb-2" href="?halaman=mahasiswa&aksi=tambah"><i class="fa fa-plus-circle"></i> Tambah Mahasiswa</a>
	
	<div class="d-block mt-4"><div class="btn-group btn-group-sm">
		<a class="btn btn-dark" href="?halaman=mahasiswa&status=aktif">Mahasiswa Aktif</a>
		<a class="btn btn-secondary" href="?halaman=mahasiswa&status=nonaktif">Mahasiswa Tidak Aktif</a>
	</div></div>

	<div class="table-responsive">
	<table class="table my-4 table-hover bg-white elv-profil-table">
		<thead class="thead-dark">
		<tr>
			<th scope="col">NIDN</th>
			<th scope="col">Nama</th>
			<th scope="col">Prodi</th>
			<th scope="col">Tindakan</th>
		</tr>
		</thead>
		<tbody>
		<?php 
		if ($get_users) {
			foreach ($get_users as $hasil) {
			$id_prodi = $hasil->id_prodi; ?>
			<tr class="tr-<?php echo $hasil->ID; ?>">
				<th scope="row"><?php echo $hasil->user_login; ?></th>
				<td><?php echo $hasil->first_name; ?></td>
				<td>
					<?php 
					if ($id_prodi) {
						$show_prodi = $wpdb->get_results("SELECT * FROM $table_prodi WHERE id_prodi =  $id_prodi");
						foreach ($show_prodi as $tampil){ 
							echo $tampil->nama_prodi; 
						} 
					}
					?>
				</td>
				<td>
				<div class="d-flex">
					<a class="lihat btn btn-primary btn-sm me-1" id="<?php echo $hasil->ID; ?>" data-bs-toggle="collapse" href="#trd-<?php echo $hasil->ID; ?>" role="button" aria-expanded="false" aria-controls="trd-<?php echo $hasil->ID; ?>"><i class="fa fa-eye"></i></a>
					<a class="edit btn btn-info btn-sm" href="<?php the_permalink(); ?>?halaman=mahasiswa&aksi=edit&id=<?php echo $hasil->ID; ?>"><i class="fa fa-pencil"></i></a>
					<a class="hapus btn btn-danger btn-sm text-white ms-1" title="Hapus" id="<?php echo $hasil->ID; ?>"><i class="fa fa-trash"></i></a>
				</div>
				</td>
			</tr>
			<tr id="trd-<?php echo $hasil->ID; ?>" class="collapse bg-info2">
			<td colspan="9" class="detailuser">
			
				<?php $stat = $hasil->status;
				if ($stat=="Aktif") {
					echo '<span class="pull-right"> Aktif <a class="ubah text-success mx-1" title="Nonaktifkan status Mahasiswa" id="'.$hasil->ID.'"><i class="fa fa-toggle-on"></i></a> </span>';
				} else {
					echo '<span class="pull-right"> Tidak Aktif <a class="ubah text-success mx-1" title="Aktifkan status Mahasiswa" id="'.$hasil->ID.'"><i class="fa fa-toggle-off"></i></a> </span>';
				}
				elv_lihatuser($profil_mahasiswa,$hasil->ID); ?>
			</td></tr>
		<?php } //endforeach 
		} else {
			echo '<td colspan="4"><div class="alert alert-danger">Oops, tidak ada data disini..</div></td>';
		}		
		?>
		</tbody>
	</table>
	</div>

	<div class="elv-pagination">
	<?php $result = count_users();
	$totaluser = $result['avail_roles']['mahasiswa'];
	echo paginate_links( array(
		'base' => add_query_arg( 'cpage', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => ceil($totaluser / $items_per_page),
		'current' => $page
	)); ?>
	</div>

	<script>
	jQuery(document).ready(function($){
	$(document).on("click",".hapus",function(e){
		if (confirm('Apakah anda yakin ingin menghapus user ini?')) {
		var get_id = $(this).attr("id");
			$.ajax({  
			type: "POST",
		<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
			data: "action=hapususer&jalankan=ya&id=" + get_id,
		<?php } ?>
			url: sia_ajaxurl,
			success:function(data) {
				$(".tr-" + get_id).remove();
				$("#trd-" + get_id).remove();
			}
			});
		}
	});
	$(document).on("click",".ubah",function(e){
		var get_id = $(this).attr("id");
		$.ajax({  
			type: "POST",
		<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
			data: "action=ubahmahasiswa&id=" + get_id,
		<?php } ?>
			url: sia_ajaxurl,
			success:function(data) {
				$(".tr-" + get_id).html('<td colspan="4"><div class="alert alert-success">Status diubah</div></td>');
				$("#trd-" + get_id).remove();
			}
		});
	});
	});
	</script>
	
<?php } ?>


