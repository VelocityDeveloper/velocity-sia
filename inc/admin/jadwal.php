<?php
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id = isset($_GET['id'])? $_GET['id'] : '';
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$table_jadwal = $wpdb->prefix . "v_jadwal";
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul");
$arr_hari = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');

if($aksi) {
	require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/admin/jadwal-aksi.php' );
} else {
	$items_per_page = 20;
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
	$offset = ( $page * $items_per_page ) - $items_per_page;
	$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_jadwal id_jadwal");
	$tampil_jadwal = $wpdb->get_results("SELECT * FROM $table_jadwal ORDER BY id_jadwal DESC LIMIT ${offset}, ${items_per_page}"); ?>

	<a class="btn btn-info mb-2" href="?halaman=jadwal&aksi=tambah"><i class="fa fa-plus-circle"></i> Tambah Jadwal</a>

	<?php if ($exist) { ?>
	
		<ul class="event-list">
		
			<?php foreach ($tampil_jadwal as $hasil) {
				$id_makul = $hasil->id_makul;
				$show_makul = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $id_makul");
				$data_makul = $show_makul[0];
				?>
				<li class="li-<?php echo $hasil->id_jadwal; ?> list">
					<div class="list-box1">
						<div class="align-self-center w-100">
							<h5><?php echo $hasil->hari; ?></h5>					
							<?php echo $hasil->jam_kuliah_awal; ?> - <?php echo $hasil->jam_kuliah_akhir; ?>
						</div>
					</div>
					<div class="list-box2">
						<h5 class="text-info"><?php echo $data_makul->nama_makul; ?></h5>
						<ul class="ul-list">
							<li>Dosen : <?php echo get_userdata($data_makul->id_dosen)->first_name; ?></li>
							<li>Kelas : <?php echo $hasil->kelas; ?></li>
							<li>Ruang : <?php echo $hasil->ruang; ?></li>
							<li>Tahun Akademik : <?php echo $data_makul->tahun_akademik; ?></li>
							<li>Semester : <?php echo $data_makul->semester; ?></li>
							<li>SKS : <?php echo $data_makul->sks; ?></li>
							<li>Kuota : <?php echo $hasil->kuota; ?></li>
						</ul>	
					</div>
					<a class="absolute-top text-secondary" data-bs-toggle="collapse" href="#trd-<?php echo $hasil->id_jadwal; ?>" role="button" aria-expanded="false" aria-controls="trd-<?php echo $hasil->id_jadwal; ?>"><i class="fa fa-ellipsis-h"></i></a>		
					<div id="trd-<?php echo $hasil->id_jadwal; ?>" class="collapse absolute-bottom">
						<ul class="list-group">
							<li class="list-group-item p-0 text-center border-0 mb-1"><a class="edit btn btn-info" href="<?php the_permalink(); ?>?halaman=jadwal&aksi=edit&id=<?php echo $hasil->id_jadwal; ?>"><i class="fa fa-pencil"></i></a></li>
							<li class="list-group-item p-0 text-center border-0"><a class="hapus btn btn-danger text-white" id="<?php echo $hasil->id_jadwal; ?>"><i class="fa fa-trash"></i></a></li>
						</ul>
					</div>
				</li>
			<?php } //endforeach ?>
		
		</ul>
		
		<script> // Skrip untuk hapus makul
		jQuery(document).ready(function($){
		$(document).on("click",".hapus",function(e){
		if (confirm('Apakah anda yakin ingin menghapus jadwal ini?')) {
		var get_id = $(this).attr("id");
			$.ajax({  
			type: "POST",
		<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
			data: "action=hapusjadwal&jalankan=ya&id=" + get_id,
		<?php } ?>
			url: sia_ajaxurl,
			success:function(data) {
				$(".li-" + get_id).remove();
			}
			});
		}
		});
		});
		</script>


		<div class="sia-pagination">
		<?php echo paginate_links( array(
			'base' => add_query_arg( 'cpage', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil($exist / $items_per_page),
			'current' => $page
		)); ?>
		</div>
		
	<?php } else {
		echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Maaf, belum ada data disini..</div>';
	}	//endif

} ?>

