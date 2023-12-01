<?php
$user_id      = get_current_user_id();
$id           = isset($_GET['id'])? $_GET['id'] : '';
$table_tugas  = $wpdb->prefix . "v_tugas";

if (!empty($id)) {
  $countjawab   = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tujuan = $id");
  $tampil_jawab = $wpdb->get_results("SELECT * FROM $table_tugas WHERE tujuan = $id ORDER BY tanggal DESC");

	$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE id = $id");
  $data = get_object_vars($tampil_tugas[0]);
  $detailt 			= $data['detail'];
  $detailc 			= json_decode($detailt);
  $namatugas		= $detailc->nama;
?>
  <!-- tampilkan detail tugas -->
  <div class="card" style="width: 18rem;">
    <div class="card-header"> <?php echo $namatugas; ?> </div>
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
         <th scope="col">File</th>
         <th scope="col">Catatan</th>
         <th scope="col">Tanggal</th>
       </tr>
       </thead>
       <tbody>
       <?php foreach ($tampil_jawab as $hasil) {
         $idj     = $hasil->id;
         $iduserj = $hasil->iduser;
         $detailj = json_decode($hasil->detail);
         $filejc	= $detailj->file;
         $filej	  = wp_get_attachment_url( $filejc );
         ?>
         <tr class="tr-<?php echo $idj; ?>">
           <td><?php echo $n++; ?></td>
           <td><?php echo get_userdata($iduserj)->first_name; ?></td>
           <td><?php echo get_userdata($iduserj)->kelas; ?></td>
           <td>
             <?php
             //jika file tersedia
             if ((isset($filej))&&(!empty($filej))) {
                 echo '<a href="'.$filej.'" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>';
             } else {
                 echo '-';
             }
             ?>
           </td>
           <td><?php echo $detailj->catatan; ?></td>
           <td><?php echo date("H:i, d/m/Y", strtotime($hasil->tanggal)); ?></td>
         </tr>
       <?php } //endforeach ?>

       </tbody>
     </table>
     </div>



  <?php } else {
      echo '<div class="alert alert-info my-3" role="alert"><i class="fa fa-info-circle"></i> Maaf, belum ada data disini..</div>';
    }	//endif tampil jawaban
  } //endif id ?>
