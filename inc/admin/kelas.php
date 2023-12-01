<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_Kelas = $wpdb->prefix . "v_kelas";

if (isset($_POST['tambah_Kelas']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$wpdb->insert($table_Kelas, array('nama' => $_POST['nama'],));
	echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data kelas berhasil ditambahkan.</div>';
	}
}


if (isset($_POST['edit_Kelas']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
		$id = isset($_POST['id'])? $_POST['id'] : '' ;
		$nama = isset($_POST['nama'])? $_POST['nama'] : '' ;
		$wpdb->update( $table_Kelas, array(
			'nama'     => $nama,
		), array('id'=> $id));
		echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data kelas "'.$id.'" berhasil diupdate.</div>';
	}
}


$items_per_page = 20;
$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
$offset = ( $page * $items_per_page ) - $items_per_page;
$tampil_Kelas = $wpdb->get_results("SELECT * FROM $table_Kelas LIMIT ${offset}, ${items_per_page}");
$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_Kelas id"); ?>

<a class="btn btn-info mb-2" data-bs-toggle="collapse" href=".elform" role="button" aria-expanded="false" aria-controls="elform">
	<i class="fa fa-plus-circle"></i> Tambah Kelas
</a>

<?php if (isset($_POST['edit_Kelas_ini']) == "ya") { // Form untuk edit Kelas
?>
	<form class="border border-info card p-4 mb-3" name="input" method="POST">
		<h6 class="elv-judulform">Edit Data Kelas <strong><?php echo $_POST['id'];?></strong></h6>
		<div class="row">
			<div class="col-md-6">
				<input required type="text" class="form-control" name="nama" value="<?php echo $_POST['nama']; ?>" placeholder="Nama Kelas" />
			</div>
			<div class="col-md-5 m-md-0 my-3">
				<input type="submit" class="btn btn-info" name="submit" value="Update">
			</div>
		</div>
		<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>" />
		<input type="hidden" name="edit_Kelas" value="ya" />
	</form>
<?php } ?>

<div class="elform collapse">
	<form class="border border-info card p-4" name="input" method="POST">
		<h6 class="elv-judulform">Tambah Kelas</h6>
		<div class="row">
			<div class="col-md-6">
				<input required type="text" class="form-control" name="nama" value="" placeholder="Nama Kelas" />
			</div>
			<div class="col-md-5 m-md-0 my-3">
				<input type="submit" name="submit" class="btn btn-info" value="Tambah">
			</div>
		</div>
		<input type="hidden" name="tambah_Kelas" value="ya" />
	</form>
</div>




<?php if ($exist) { ?>
<div class="table-responsive">
	<table class="table my-4 table-hover bg-white">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Kode Kelas</th>
			<th scope="col">Nama Kelas</th>
			<th scope="col">Tindakan</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($tampil_Kelas as $hasil) { ?>
		<tr class="tr-<?php echo $hasil->id; ?>">
			<th scope="row"><?php echo $hasil->id; ?></th>
			<td><?php echo $hasil->nama; ?></td>
			<td>
			<div class="d-flex">
				<form method="POST">
					<input type="hidden" name="id" value="<?php echo $hasil->id; ?>" />
					<input type="hidden" name="nama" value="<?php echo $hasil->nama; ?>" />
					<input type="hidden" name="edit_Kelas_ini" value="ya" />
					<button class="btn btn-info btn-sm" title="Edit" title="Edit"><i class="fa fa-pencil"></i></button>
				</form>
				<a class="hapus btn btn-danger btn-sm text-white ms-1" title="Hapus" id="<?php echo $hasil->id; ?>" title="Hapus">
					<i class="fa fa-trash"></i>
				</a>
			</div>
			</td>
		</tr>
		<?php } //endforeach ?>
		</tbody>
	</table>
</div>

	<script> // Skrip untuk hapus
	jQuery(document).ready(function($){
		$(document).on("click",".hapus",function(e){
			if (confirm('Apakah anda yakin ingin menghapus data ini?')) {
				var get_id = $(this).attr("id");
				$.ajax({  
					type: "POST",
				<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
					data: "action=hapuskelas&jalankan=ya&id=" + get_id,
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

