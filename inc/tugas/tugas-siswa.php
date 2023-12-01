<?php
$user_id 			= get_current_user_id();
$date		 			= date( 'd-m-Y H:i:s', current_time( 'timestamp', 0 ) );
$infouser 		= get_userdata($user_id);
$id_semester 	= get_user_meta($user_id,'semester',true);
$thn_ajaran 	= get_user_meta($user_id,'angkatan',true);
$prodiku 			= get_user_meta($user_id,'id_prodi',true);
$kelasku 			= get_user_meta($user_id,'kelas',true);
$aksi 				= isset($_GET['aksi'])? $_GET['aksi'] : '';
$id 					= isset($_GET['id'])? $_GET['id'] : '';
$set 					= isset($_GET['set'])? $_GET['set'] : '';
$table_tugas 	= $wpdb->prefix . "v_tugas";
$table_prodi 	= $wpdb->prefix . "v_prodi";
$table_makul	= $wpdb->prefix . "v_mata_kuliah";
$table_krs 		= $wpdb->prefix . "v_krs";
$table_jadwal = $wpdb->prefix . "v_jadwal";

if($aksi) {
	require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/tugas/tugas-kerjakan.php' );
} else {

  $get_jadwal = $wpdb->get_results("
  SELECT * FROM $table_jadwal JOIN $table_makul
  ON $table_jadwal.id_makul = $table_makul.id_makul
  JOIN $table_krs ON $table_krs.id_jadwal = $table_jadwal.id_jadwal
  WHERE $table_makul.tahun_akademik = $thn_ajaran AND $table_makul.semester = $id_semester AND $table_makul.id_prodi = $prodiku AND $table_krs.id_mahasiswa = $user_id");

	//jika siswa sudah mengambil krs
	if ($get_jadwal) {

		//ambil id tugas yang sudah dikerjakan
		$sudahjawab 		= $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'jawab' and iduser = $user_id ");
		if ($sudahjawab) {
			$idtugask				= array();
			foreach ($sudahjawab as $sd) {
				$idtugask[] = $sd->tujuan;
			}
			if ($set) {
				$idtugask = array_map(function($v) {
				    return "'%" . esc_sql($v) . "%'";
				}, $idtugask);
				$idtugask = implode(' or id like ', $idtugask);
			} else {
				$idtugask = array_map(function($v) {
						return "'" . esc_sql($v) . "'";
				}, $idtugask);
				$idtugask = implode(' or id != ', $idtugask);
			}
		} else {
			$idtugask = '0';
		}

		//ambil id matakuliah
		$idmatakul = array();
	  foreach ($get_jadwal as $jadwal) {
	    $idmatakul[] = $jadwal->id_makul;
	  }
		$idmatakul = array_map(function($v) {
		    return "'%" . esc_sql($v) . "%'";
		}, $idmatakul);
		$idmatakul = implode(' or tujuan like ', $idmatakul);

		$items_per_page = 30;
		$page 					= isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset 				= ( $page * $items_per_page ) - $items_per_page;

		if ($set) {
			//tampil tugas sudah dikerjakan
			$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tipe = 'tugas' and ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') and ( id like $idtugask )");
			$tampil_tugas 	= $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'tugas' and ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') and ( id like $idtugask ) ORDER BY tanggal DESC LIMIT ${offset}, ${items_per_page}");
			$act = 'sudah';
		} else {
			$exist = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tipe = 'tugas' and ( id != $idtugask ) and ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') ");
			$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'tugas' and ( id != $idtugask ) and ( tujuan like $idmatakul ) and (tujuan like '%$kelasku%') ORDER BY tanggal DESC LIMIT ${offset}, ${items_per_page}");
			$act = 'belum';
		}
	?>

			<div class="btn-group" role="group">
			  <a class="btn <?php if ($set) { echo 'btn-secondary';} else { echo 'btn-info';}?>" href="?halaman=tugas">Belum Dikerjakan</a>
				<a class="btn <?php if ($set) { echo 'btn-info';} else { echo 'btn-secondary';}?>" href="?halaman=tugas&set=sudah">Sudah Dikerjakan</a>
			</div>

			<div class="table-responsive">
			<table class="table my-4 table-hover bg-white elv-profil-table">
				<thead class="thead-dark">
				<tr>
					<th scope="col">Tugas</th>
					<th scope="col">Dosen</th>
					<th scope="col">Matkul</th>
					<th scope="col"></th>
				</tr>
				</thead>
				<tbody>
				<?php if ($tampil_tugas) {
					foreach ($tampil_tugas as $hasil) {		
						$id 			= $hasil->id;
						$sudahjawab 	= $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'jawab' and tujuan = $id and iduser = $user_id");
						if(empty($sudahjawab) && $act == 'belum' || $sudahjawab && $act == 'sudah'){
							$iduser 		= $hasil->iduser;
							$detail			= json_decode($hasil->detail);
							$tujuanc 		= json_decode($hasil->tujuan);
							$idmatkul		= $tujuanc->mata_kuliah;
							$show_makul 	= $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $idmatkul");
							$data_makul 	= $show_makul[0];
							$bataswaktu 	= $detail->bataswaktu;
							$datex 			= date("U", strtotime($date));
							$bataswaktux	= date("U", strtotime($bataswaktu));
							$status			= '';
							if ((!empty($bataswaktu)) && ($datex > $bataswaktux)) {
								$status		.= 'alert-danger';
							} ?>
							<tr class="tr-<?php echo $id; ?> tr-tugas <?php echo $status; ?>">
								<td><a class="lihat" href="#" onclick="return false" id="<?php echo $id; ?>"><?php echo $detail->nama; ?></a></td>
								<td><?php echo get_userdata($iduser)->first_name; ?></td>
								<td><?php echo $data_makul->nama_makul; ?></td>
								<td class="text-right"><?php echo date("d M", strtotime($hasil->tanggal)); ?></td>
							</tr>
	      		<?php }
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

		<?php
	} else {
		echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Kamu belum ambil KRS</div>';
	}
} ?>
