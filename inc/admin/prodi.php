<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_prodi = $wpdb->prefix . "v_prodi";
$table_fakultas = $wpdb->prefix . "v_fakultas";
if (isset($_POST['tambah_prodi']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$wpdb->insert($table_prodi, array(
		'nama_prodi' => $_POST['nama_prodi'],
		'id_fakultas' => $_POST['id_fakultas'],
		)
	);
	echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data berhasil ditambahkan.</div>';
	}
}


if (isset($_POST['edit_prodi']) == "ya"){
	if (!(wp_get_current_user()->user_login == "admindemo")){
	$id_prodi = isset($_POST['id_prodi'])? $_POST['id_prodi'] : '' ;
	$nama_prodi = isset($_POST['nama_prodi'])? $_POST['nama_prodi'] : '' ;
	$id_fakultas = isset($_POST['id_fakultas'])? $_POST['id_fakultas'] : '' ;
	$wpdb->update( $table_prodi, array(
		'nama_prodi'     => $nama_prodi,
		'id_fakultas'     => $id_fakultas,
	), array('id_prodi'=> $id_prodi));
	echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Data "'.$id_prodi.'" berhasil diupdate.</div>';
	}
}


$items_per_page = 20;
$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
$offset = ( $page * $items_per_page ) - $items_per_page;
$tampil_prodi = $wpdb->get_results("SELECT * FROM $table_prodi LIMIT ${offset}, ${items_per_page}");
$tampil_fakultas = $wpdb->get_results("SELECT * FROM $table_fakultas");
$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_prodi id_prodi"); ?>

<button class="btn btn-info mb-2" data-bs-toggle="collapse" href=".elform" role="button" aria-expanded="false" aria-controls="elform">
	<i class="fa fa-plus-circle"></i> Tambah Prodi
</button>

<?php if (isset($_POST['edit_prodi_ini']) == "ya") { // Form untuk edit prodi
$id_fakultas = $_POST['id_fakultas']; ?>
	<form class="border border-info card p-4 mb-3" name="input" method="POST">
	<h6 class="elv-judulform">Edit Prodi <strong><?php echo $_POST['id_prodi'];?></strong></h6>
		<div class="form-group mb-3">
			<label for="nama_prodi">Nama Program Studi</label>		
			<input required type="text" class="form-control" name="nama_prodi" value="<?php echo $_POST['nama_prodi']; ?>" placeholder="Nama Prodi" />
		</div>
		<div class="form-group mb-3">
			<label for="id_fakultas">Fakultas</label>		
			<select name="id_fakultas" class="form-control">
				<?php $show_fakultas = $wpdb->get_results("SELECT * FROM $table_fakultas WHERE id_fakultas");
				foreach ($show_fakultas as $tampil){
				$id_f = $tampil->id_fakultas; ?>
					<option value="<?php echo $id_f; ?>" <?php if ($id_f==$id_fakultas){echo "selected";};?>><?php echo $tampil->nama_fakultas; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-info" name="submit" value="Update"></td>
		</div>
	<input type="hidden" name="id_prodi" value="<?php echo $_POST['id_prodi']; ?>" />
	<input type="hidden" name="edit_prodi" value="ya" />
	</form>
<?php } ?>


<div class="elform collapse">
	<form class="border border-info card p-4" name="input" method="POST">
	<h6 class="elv-judulform">Tambah Program Studi</h6>
		<div class="form-group mb-3">
			<label for="nama_prodi">Nama Program Studi</label>
			<input required class="form-control" type="text" name="nama_prodi" value="" placeholder="Nama Prodi" />
		</div>
		<div class="form-group mb-3">
			<label for="id_fakultas">Fakultas</label>	
			<select name="id_fakultas" class="form-control">
				<?php foreach ($tampil_fakultas as $fakultas) { ?>
					<option value="<?php echo $fakultas->id_fakultas; ?>"><?php echo $fakultas->nama_fakultas; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-info" name="submit" value="Tambah">
		</div>
	<input type="hidden" name="tambah_prodi" value="ya" />
	</form>
</div>




<?php if ($exist) { ?>
<div class="table-responsive">
	<table class="table my-4 table-hover">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Kode Prodi</th>
			<th scope="col">Nama Prodi</th>
			<th scope="col">Fakultas</th>
			<th scope="col">Tindakan</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($tampil_prodi as $hasil) {
			$id_fakultas = $hasil->id_fakultas; ?>
			<tr class="tr-<?php echo $hasil->id_prodi; ?>">
				<th scope="row"><?php echo $hasil->id_prodi; ?></th>
				<td><?php echo $hasil->nama_prodi; ?></td>
				<?php $show_fakultas = $wpdb->get_results("SELECT * FROM $table_fakultas WHERE id_fakultas =  $id_fakultas");
				foreach ($show_fakultas as $tampil){ ?>
					<td><?php echo $tampil->nama_fakultas; ?></td>
				<?php } ?>
				<td>
				<div class="d-flex">
					<form method="POST">
					<input type="hidden" name="id_prodi" value="<?php echo $hasil->id_prodi; ?>" />
					<input type="hidden" name="nama_prodi" value="<?php echo $hasil->nama_prodi; ?>" />
					<input type="hidden" name="id_fakultas" value="<?php echo $hasil->id_fakultas; ?>" />
					<input type="hidden" name="edit_prodi_ini" value="ya" />
					<button class="btn btn-info btn-sm" title="Edit"><i class="fa fa-pencil"></i></button>
					</form>
					<a class="hapus btn btn-danger btn-sm text-white ms-1" id="<?php echo $hasil->id_prodi; ?>"><i class="fa fa-trash"></i></a>
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
		if (confirm('Apakah anda yakin ingin menghapus data ini?')) {
			var get_id = $(this).attr("id");
			$.ajax({
				type: "POST",
			<?php if (!(wp_get_current_user()->user_login == "admindemo")){ ?>
				data: "action=hapusprodi&jalankan=ya&id=" + get_id,
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