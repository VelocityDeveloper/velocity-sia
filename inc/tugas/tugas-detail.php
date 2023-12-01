<?php
global $wpdb;
$user_id 			= get_current_user_id();
$date		 			= date( 'd-m-Y H:i:s', current_time( 'timestamp', 0 ) );
$id 					= isset($_POST['id'])?$_POST['id'] : '';
$table_tugas 	= $wpdb->prefix . "v_tugas";
$table_makul 	= $wpdb->prefix . "v_mata_kuliah";
$tampil_tugas = $wpdb->get_results("SELECT * FROM $table_tugas WHERE id = $id");
$data 				= get_object_vars($tampil_tugas[0]);
$iduserc			= $data['iduser'];
$tanggalc			= $data['tanggal'];
$tujuant 			= $data['tujuan'];
$detailt 			= $data['detail'];
$tanggal 			= date('d M Y, H:i', strtotime($tanggalc));
$detailc 			= json_decode($detailt);
$nama					= $detailc->nama;
$filec				= $detailc->file;
$file					= wp_get_attachment_url( $filec );
$catatan			= $detailc->catatan;
$bataswaktuc	= $detailc->bataswaktu;
$tujuanc 			= json_decode($tujuant);
$idmatkul			= $tujuanc->mata_kuliah;
$idkelas			= $tujuanc->kelas;
$show_makul 	= $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $idmatkul");
$data_makul 	= get_object_vars($show_makul[0]);
if ($bataswaktuc) {
    $bataswaktu 	= date('d M Y, H:i', strtotime($bataswaktuc));
} else {
    $bataswaktu 	= ' - ';
}
$datex 				= date("U", strtotime($date));
$bataswaktux	= date("U", strtotime($bataswaktuc));
$dikerjakan   = $wpdb->get_var("SELECT COUNT(*) FROM $table_tugas id WHERE tujuan = $id");

?>

  <div id="mdl-<?php echo $id; ?>" class="modal fade show" style="padding-right: 15px;background: #0006;display: block;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-detail">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $nama; ?></h5>
            <button type="button" class="btn-close close tutup" id="<?php echo $id; ?>"></button>
        </div>
        <div class="modal-body">

            <?php if(!current_user_can('mahasiswa')){
                echo '<div class="alert alert-info" role="alert"> Total Dikerjakan : <strong>'.$dikerjakan.'</strong></div>';
              }
            ?>

            <table class="table"><tbody>
            <tr><td style="border: 0;">Dosen</td><td style="border: 0;">:</td><td style="border: 0;"><?php echo get_userdata($iduserc)->first_name; ?> </td></tr>
            <tr><td>File</td><td>:</td><td>
            <?php
            //jika file tersedia
            if ((isset($file))&&(!empty($file))) {
                echo '<h1><i class="fa fa-file-text" aria-hidden="true"></i></h1><a href="'.$file.'" target="_blank" class="d-block">Klik untuk unduh</a>';
            } else {
                echo '<span class="fa-stack fa-lg"><i class="fa fa-file-text fa-stack-1x"></i> <i class="fa fa-ban fa-stack-2x text-danger"></i></span> Tidak ada File';
            }
            ?>
            </td></tr>
            <tr><td>Catatan</td><td>:</td><td><?php echo $catatan; ?></td></tr>
            <tr><td>Mata Kuliah</td><td>:</td><td><?php echo $data_makul['nama_makul'] ?></td></tr>
            <tr><td>Kelas</td><td>:</td><td><ul class="pl-3">
              <?php
              foreach ( $idkelas as $kelas ) {
                echo '<li>'.$kelas.'</li>';
              }
              ?>
            </ul></td></tr>
            <tr><td>Tanggal</td><td>:</td><td><?php echo $tanggal; ?></td></tr>
            <tr><td>Batas Waktu</td><td>:</td><td><?php echo $bataswaktu; ?></td></tr>

            </tbody></table>
        </div>
        <div class="modal-footer">
              <?php
              //jika yang login mahasiswa
              if(current_user_can('mahasiswa')){
                //cek jika sudah mengerjakan
                  $sudahjawab 	= $wpdb->get_results("SELECT * FROM $table_tugas WHERE tipe = 'jawab' and tujuan = $id and iduser = $user_id");
                  if ($sudahjawab) {
                    echo '<a href="?halaman=tugas&aksi=kerjakan&id='.$id.'" class="btn btn-warning">Sudah dikerjakan</a>';
                  } else {
                    //cek waktu
                    if ((!empty($bataswaktuc)) && ($datex > $bataswaktux)) {
                      echo '<button class="btn btn-danger">Waktu Habis</button>';
                    } else {
                      echo '<a href="?halaman=tugas&aksi=kerjakan&id='.$id.'" class="btn btn-info">Kerjakan</a>';
                    }
                  }
              } else {
                  echo '<a href="?halaman=tugas&list=jawaban&id='.$id.'" class="btn btn-info">Daftar Jawaban</a>';
                  echo '<a href="?halaman=tugas&aksi=edit&id='.$id.'" class="btn btn-info">Edit</a>';
              }
              ?>
              <button type="button" class="btn btn-secondary tutup" id="<?php echo $id; ?>">Tutup</button>
          </div>
        </div>
      </div>
    </div>
