<?php
$user_id 			= get_current_user_id();
$infouser 		= get_userdata($user_id);
$id_semester 	= get_user_meta($user_id,'semester',true);
$thn_ajaran 	= get_user_meta($user_id,'angkatan',true);
$prodiku 			= get_user_meta($user_id,'id_prodi',true);
$kelasku 			= get_user_meta($user_id,'kelas',true);
$table_materi = $wpdb->prefix . "v_materi";
$table_makul	= $wpdb->prefix . "v_mata_kuliah";
$table_krs 		= $wpdb->prefix . "v_krs";
$table_jadwal = $wpdb->prefix . "v_jadwal";

  $get_jadwal = $wpdb->get_results("
  SELECT * FROM $table_jadwal JOIN $table_makul
  ON $table_jadwal.id_makul = $table_makul.id_makul
  JOIN $table_krs ON $table_krs.id_jadwal = $table_jadwal.id_jadwal
  WHERE $table_makul.tahun_akademik = $thn_ajaran AND $table_makul.semester = $id_semester AND $table_makul.id_prodi = $prodiku AND $table_krs.id_mahasiswa = $user_id");

	//jika siswa sudah mengambil krs
	if ($get_jadwal) {

		//ambil id matakuliah
		$idmatakul = array();
	  foreach ($get_jadwal as $jadwal) {
	    $idmatakul[] = $jadwal->id_makul;
	  }
		$idmatakul = array_map(function($v) {
		    return "'%" . esc_sql($v) . "%'";
		}, $idmatakul);
		$idmatakul = implode(' or tujuan like ', $idmatakul);

    $n = 1;
		$items_per_page = 30;
		$page 					= isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset 				= ( $page * $items_per_page ) - $items_per_page;

		$exist 					= $wpdb->get_var("SELECT COUNT(*) FROM $table_materi id WHERE ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') ");
		$tampil_materi 	= $wpdb->get_results("SELECT * FROM $table_materi WHERE ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') ORDER BY tanggal DESC LIMIT ${offset}, ${items_per_page}");

	?>

			<div class="table-responsive">
			<table class="table my-4 table-hover bg-white elv-profil-table">
				<thead class="thead-dark">
				<tr>
					<th scope="col">No</th>
          <th scope="col">Materi</th>
					<th scope="col">Dosen</th>
					<th scope="col">Matkul</th>
					<th scope="col"></th>
				</tr>
				</thead>
				<tbody>
				<?php if ($tampil_materi) {
					foreach ($tampil_materi as $hasil) {
					$id 					= $hasil->id;
					$iduser 			= $hasil->iduser;
					$detail				= json_decode($hasil->detail);
					$tujuanc 			= json_decode($hasil->tujuan);
					$idmatkul			= $tujuanc->mata_kuliah;
					$show_makul 	= $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $idmatkul");
					$data_makul 	= $show_makul[0];
	      ?>
	        			<tr class="tr-<?php echo $id; ?>">
                    <td><?php echo $n++; ?></td>
	        					<td><a class="lihat" href="#" onclick="return false" id="<?php echo $id; ?>"><?php echo $detail->nama; ?></a></td>
	        					<td><?php echo get_userdata($iduser)->first_name; ?></td>
										<td><?php echo $data_makul->nama_makul; ?></td>
	        					<td class="text-right"><?php echo date("d M", strtotime($hasil->tanggal)); ?></td>
	        			</tr>
	      <?php
	    	}
			 	//endforeach
			 } else {
					echo '<tr><td colspan="4"><div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Maaf, belum ada data disini..</div></td></tr>';
				}	//endif ?>

				</tbody>
			</table>
			</div>

			<div id="show765"></div>

			<script> // Skrip
			jQuery(document).ready(function($){
			$(document).on("click",".lihat",function(e){
				$("#show765").html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
				var get_id = $(this).attr("id");
				$.ajax({
					type: "POST",
					data: "action=lihatmateri&id=" + get_id,
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

		<?php
	} else {
		echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Kamu belum ambil KRS</div>';
	}
 ?>
