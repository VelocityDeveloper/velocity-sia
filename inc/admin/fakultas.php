<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_fakultas = $wpdb->prefix . "v_fakultas";

if (isset($_POST['tambah_fakultas']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$wpdb->insert($table_fakultas, array('nama_fakultas' => $_POST['nama_fakultas'],));
	echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data Fakultas berhasil ditambahkan.</div>';
	}
}


if (isset($_POST['edit_fakultas']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
		$id_fakultas = isset($_POST['id_fakultas'])? $_POST['id_fakultas'] : '' ;
		$nama_fakultas = isset($_POST['nama_fakultas'])? $_POST['nama_fakultas'] : '' ;
		$wpdb->update( $table_fakultas, array(
			'nama_fakultas'     => $nama_fakultas,
		), array('id_fakultas'=> $id_fakultas));
		echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data Fakultas "'.$id_fakultas.'" berhasil diupdate.</div>';
	}
}


$items_per_page = 20;
$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
$offset = ( $page * $items_per_page ) - $items_per_page;
$tampil_fakultas = $wpdb->get_results("SELECT * FROM $table_fakultas LIMIT ${offset}, ${items_per_page}");
$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_fakultas id_fakultas"); ?>

<a class="btn btn-info mb-2" data-bs-toggle="collapse" href="#elform" role="button" aria-expanded="false" aria-controls="elform">
	<i class="fa fa-plus-circle"></i> Tambah Fakultas
</a>

<?php if (isset($_POST['edit_fakultas_ini']) == "ya") { // Form untuk edit Fakultas
?>
	<form class="border border-info card p-4 mb-3" name="input" method="POST">
		<h6 class="elv-judulform">Edit Data Fakultas <strong><?php echo $_POST['id_fakultas'];?></strong></h6>
		<div class="row">
			<div class="col-md-6">
				<input required type="text" class="form-control" name="nama_fakultas" value="<?php echo $_POST['nama_fakultas']; ?>" placeholder="Nama Fakultas" />
			</div>
			<div class="col-md-5 m-md-0 my-3">
				<input type="submit" class="btn btn-info" name="submit" value="Update">
			</div>
		</div>
		<input type="hidden" name="id_fakultas" value="<?php echo $_POST['id_fakultas']; ?>" />
		<input type="hidden" name="edit_fakultas" value="ya" />
	</form>
<?php } ?>

<div class="collapse" id="elform">
	<form class="border border-info card p-4" name="input" method="POST">
		<h6 class="elv-judulform">Tambah Fakultas</h6>
		<div class="row">
			<div class="col-md-6">
				<input required type="text" class="form-control" name="nama_fakultas" value="" placeholder="Nama Fakultas" />
			</div>
			<div class="col-md-5 m-md-0 my-3">
				<input type="submit" name="submit" class="btn btn-info" value="Tambah">
			</div>
		</div>
		<input type="hidden" name="tambah_fakultas" value="ya" />
	</form>
</div>




<?php if ($exist) { ?>
<div class="table-responsive">
	<table class="table my-4 table-hover bg-white">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Kode Fakultas</th>
			<th scope="col">Nama Fakultas</th>
			<th scope="col">Tindakan</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($tampil_fakultas as $hasil) { ?>
		<tr class="tr-<?php echo $hasil->id_fakultas; ?>">
			<th scope="row"><?php echo $hasil->id_fakultas; ?></th>
			<td><?php echo $hasil->nama_fakultas; ?></td>
			<td>
			<div class="d-flex">
				<form method="POST">
					<input type="hidden" name="id_fakultas" value="<?php echo $hasil->id_fakultas; ?>" />
					<input type="hidden" name="nama_fakultas" value="<?php echo $hasil->nama_fakultas; ?>" />
					<input type="hidden" name="edit_fakultas_ini" value="ya" />
					<button class="btn btn-info btn-sm" title="Edit" title="Edit"><i class="fa fa-pencil"></i></button>
				</form>
				<a class="hapus btn btn-danger btn-sm text-white ms-1" title="Hapus" id="<?php echo $hasil->id_fakultas; ?>" title="Hapus">
					<i class="fa fa-trash"></i>
				</a>
			</div>
			</td>
		</tr>
		<?php } //endforeach ?>
		</tbody>
	</table>
</div>
<script> // Skrip untuk hapus makul
	jQuery(document).ready(function($){
		$(document).on("click",".hapus",function(e){
			var get_id = $(this).attr("id");
			if (confirm('Apakah anda yakin ingin menghapus data ini?')) {
				$.ajax({
					type: "POST",
				<?php if(!(wp_get_current_user()->user_login == "admindemo")){ ?>
					data: "action=hapusfakultas&jalankan=ya&id=" + get_id,
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
}	//endif ?>

