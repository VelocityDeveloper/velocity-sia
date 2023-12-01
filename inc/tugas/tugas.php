<?php
$user_id = get_current_user_id();
$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$list = isset($_GET['list'])? $_GET['list'] : '';
$id = isset($_GET['id'])? $_GET['id'] : '';
$table_tugas = $wpdb->prefix . "v_tugas";

if($aksi) {
	require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/tugas/tugas-aksi.php' );
} else if($list) {
	require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/tugas/tugas-list-jawaban.php' );
} else {
	$items_per_page = 30;
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
	$offset = ( $page * $items_per_page ) - $items_per_page;

	if(current_user_can('administrator')){
		$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tipe = 'tugas'");
		$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'tugas' ORDER BY id DESC LIMIT ${offset}, ${items_per_page}");
	} if(current_user_can('dosen')){
		$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tipe = 'tugas' AND iduser=$user_id");
		$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'tugas' AND iduser=$user_id ORDER BY id DESC LIMIT ${offset}, ${items_per_page}");
	}
	?>

	<a class="btn btn-info mb-2" href="?halaman=tugas&aksi=tambah"><i class="fa fa-plus-circle"></i> Tambah Tugas</a>

	<?php if ($tampil_tugas) { ?>

		<div class="table-responsive">
		<table class="table my-4 table-hover bg-white elv-profil-table">
			<thead class="thead-dark">
			<tr>
				<th scope="col">Tugas</th>
				<th scope="col">Dosen</th>
				<th scope="col">Tanggal</th>
				<th scope="col" style="width: 85px;"></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($tampil_tugas as $hasil) {
				$id = $hasil->id;
				$iduser = $hasil->iduser;
				$detail = json_decode($hasil->detail);
				?>
				<tr class="tr-<?php echo $id; ?>">
					<td><a class="lihat" href="#" onclick="return false" id="<?php echo $id; ?>"><?php echo $detail->nama; ?></a></td>
					<td><?php echo get_userdata($iduser)->first_name; ?></td>
					<td><?php echo date("d/m/Y", strtotime($hasil->tanggal)); ?></td>
					<td style="width: 85px;">
						<div class="d-flex">
							<a class="edit btn-info btn-sm btn" href="<?php the_permalink(); ?>?halaman=tugas&aksi=edit&id=<?php echo $id; ?>"><i class="fa fa-pencil"></i></a>
							<a class="hapus btn btn-danger btn-sm text-white ms-1" id="<?php echo $id; ?>"><i class="fa fa-trash"></i></a>
						</div>
					</td>
				</tr>
			<?php } //endforeach ?>

			</tbody>
		</table>
		</div>

		<div id="show765"></div>

		<script> 
		jQuery(document).ready(function($){
		$(document).on("click",".hapus",function(e){
			if (confirm('Apakah anda yakin ingin menghapus ini?')) {
			var get_id = $(this).attr("id");
			$.ajax({
				type: "POST",
				data: "action=hapustugas&id=" + get_id,
				url: sia_ajaxurl,
				success:function(data) {
					$(".tr-" + get_id).remove();
				},
				error: function(xhr, status, error) {
					var err = eval("(" + xhr.responseText + ")");
					alert(err.Message);
				}
			});
			}
		});
		$(document).on("click",".lihat",function(e){
			$("#show765").html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
			var get_id = $(this).attr("id");
			$.ajax({
				type: "POST",
				data: "action=lihattugas&id=" + get_id,
				url: sia_ajaxurl,
				success:function(data) {
					$("#show765").html(data);
				}
			});
		});
		$(document).on("click",".tutup",function(e){
			var get_id = $(this).attr("id");
			$("#mdl-" + get_id).remove();
		});
		});
		</script>

		<div class="elv-pagination">
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
