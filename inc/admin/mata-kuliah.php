<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_makul = $wpdb->prefix . "v_mata_kuliah";
$table_prodi = $wpdb->prefix . "v_prodi";
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';


if($aksi) {
	require VELOCITY_SIA_PLUGIN_DIR . '/inc/admin/mata-kuliah-action.php';
} else {

	$items_per_page = 20;
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
	$offset = ( $page * $items_per_page ) - $items_per_page;
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul ORDER BY id_makul DESC LIMIT ${offset}, ${items_per_page}");
	$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_makul id_makul");
 ?>
	<a class="btn btn-info mb-2" href="?halaman=makul&aksi=tambah"><i class="fa fa-plus-circle"></i> Tambah Mata Kuliah</a>
	<?php if ($exist) { ?>
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
				<th scope="col">Tindakan</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($tampil_makul as $hasil) {
			$id_prodi = $hasil->id_prodi;
			$id_dosen = $hasil->id_dosen; ?>
			<tr class="tr-<?php echo $hasil->id_makul; ?>">
				<td><a href="<?php the_permalink(); ?>?halaman=tambahkhs&id_makul=<?php echo $hasil->id_makul; ?>" title="KHS <?php echo $hasil->nama_makul; ?>"><?php echo $hasil->nama_makul; ?></a></td>
				<td><?php echo $hasil->tahun_akademik; ?></td>
				<td><?php echo $hasil->semester; ?></td>
				<td><?php echo $hasil->jenis_makul; ?></td>
				<td><?php echo $hasil->sks; ?></td>
				<td><?php echo get_userdata($id_dosen)->first_name; ?></td>
				<?php $show_prodi = $wpdb->get_results("SELECT * FROM $table_prodi WHERE id_prodi =  $id_prodi");
				foreach ($show_prodi as $tampil){ ?>
				<td><?php echo $tampil->nama_prodi; ?></td>
				<?php } ?>
				<td>
					<div class="d-flex">
						<a class="edit btn-info btn-sm btn" href="<?php the_permalink(); ?>?halaman=makul&aksi=edit&id_makul=<?php echo $hasil->id_makul; ?>"><i class="fa fa-pencil"></i></a>
						<a class="hapus btn btn-danger btn-sm text-white ms-1" id="<?php echo $hasil->id_makul; ?>"><i class="fa fa-trash"></i></a> 
					</div>
				</td>
			</tr>
			<?php } //endforeach ?>
			</tbody>
		</table>
	</div>
		
		<script> 
		jQuery(document).ready(function($){
			$(document).on("click",".hapus",function(e){
			if (confirm('Apakah anda yakin ingin menghapus data ini?')) {
				var get_id = $(this).attr("id");
				$.ajax({  
					type: "POST",
				<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
					data: "action=hapusmakul&jalankan=ya&id=" + get_id, 
				<?php } ?>
					url: sia_ajaxurl,
					success:function(data) {
						$(".tr-" + get_id).remove();
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
