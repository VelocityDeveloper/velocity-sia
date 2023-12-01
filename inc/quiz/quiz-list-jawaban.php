<?php
$user_id     = get_current_user_id();
$id          = isset($_GET['id'])? $_GET['id'] : '';
$table_quiz  = $wpdb->prefix . "v_quiz";

if (!empty($id)) {
  $countjawab   = $wpdb->get_var("SELECT COUNT(*) FROM $table_quiz id WHERE tujuan = $id");
  $tampil_jawab = $wpdb->get_results("SELECT * FROM $table_quiz WHERE tujuan = $id ORDER BY tanggal DESC");

	$tampil_quiz = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
  $data = get_object_vars($tampil_quiz[0]);
  $detailt 		= $data['detail'];
  $detail 		= json_decode($detailt);
  $namaquiz   = $detail->nama;
?>
  <!-- tampilkan detail quiz -->
  <div class="card" style="width: 18rem;">
    <div class="card-header"> <?php echo $namaquiz; ?> </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">Total : <?php echo $countjawab; ?></li>
    </ul>
  </div>

  <?php
  $n = 1;
  if ($tampil_jawab) {
  ?>

     <div class="table-responsive">
     <table class="table my-4 table-hover bg-white elv-profil-table">
       <thead class="thead-dark">
       <tr>
         <th scope="col">No</th>
         <th scope="col">Nama</th>
         <th scope="col">Kelas</th>
         <th scope="col">Nilai</th>
         <th scope="col">Tanggal</th>
         <th scope="col" style="width: 95px;"></th>
       </tr>
       </thead>
       <tbody>
       <?php foreach ($tampil_jawab as $hasil) {
         $idj     = $hasil->id;
         $iduserj = $hasil->iduser;
         $detailj = json_decode($hasil->detail);
         ?>
         <tr class="tr-<?php echo $idj; ?>">
           <td><?php echo $n++; ?></td>
           <td><?php echo get_userdata($iduserj)->first_name; ?></td>
           <td><?php echo get_userdata($iduserj)->kelas; ?></td>
           <td><?php echo $detailj->nilai; ?></td>
           <td><?php echo date("H:i, d/m/Y", strtotime($hasil->tanggal)); ?></td>
           <td class="d-flex" style="width: 95px;">
             <a id="<?php echo $idj; ?>" class="lihat btn btn-primary btn-sm text-white"><i class="fa fa-eye"></i></a>
             <a class="hapus btn btn-danger btn-sm text-white ms-1" id="<?php echo $idj; ?>"><i class="fa fa-trash"></i></a>
           </td>
         </tr>
       <?php } //endforeach ?>

       </tbody>
     </table>
     </div>

     <div id="show765"></div>

     <script>
   		jQuery(document).ready(function($){
  			$(document).on("click",".lihat",function(e){
  				$("#show765").html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
  				var get_id = $(this).attr("id");
  				$.ajax({
  					type: "POST",
  					data: "action=lihathasilquiz&id=" + get_id,
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
  			$(document).on("click",".hapus",function(e){
  				if (confirm('Apakah anda yakin ingin menghapus ini?')) {
  				var get_id = $(this).attr("id");
  				$.ajax({
  					type: "POST",
  					data: "action=hapusquiz&id=" + get_id,
  					url: sia_ajaxurl,
  					success:function(data) {
  						$(".tr-" + get_id).remove();
  					}
  				});
  				}
  			});
      });
    </script>

  <?php } else {
      echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Maaf, belum ada data disini..</div>';
    }	//endif tampil jawaban
  } //endif id ?>
