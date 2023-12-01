<?php
global $wpdb;
$user_id 			= get_current_user_id();
$date		 			= date( 'd-m-Y H:i:s', current_time( 'timestamp', 0 ) );
$id 					= isset($_POST['id'])?$_POST['id'] : '';
$table_quiz 	= $wpdb->prefix . "v_quiz";
$table_makul 	= $wpdb->prefix . "v_mata_kuliah";
$tampil_quiz  = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
$data 				= $tampil_quiz[0];
$iduserc			= $data->iduser;
$tanggalc			= $data->tanggal;
$tujuant 			= $data->tujuan;
$detailt 			= $data->detail;
$tanggal 			= date('d M Y, H:i', strtotime($tanggalc));
$detailc 			= json_decode($detailt);
$nama					= $detailc->nama;
$catatan			= $detailc->catatan;
$waktu			  = $detailc->waktu ? $detailc->waktu.' menit' : 'Tanpa batas waktu';
$tujuanc 			= json_decode($tujuant);
$idmatkul			= $tujuanc->mata_kuliah;
$idkelas			= $tujuanc->kelas;
$show_makul 	= $wpdb->get_results("SELECT * FROM $table_makul WHERE id_makul = $idmatkul");
$data_makul 	= $show_makul[0];
$dikerjakan   = $wpdb->get_var("SELECT COUNT(*) FROM $table_quiz id WHERE tujuan = $id");
?>

  <div id="mdl-<?php echo $id; ?>" class="modal fade show" style="padding-right: 15px;background: #0006;display: block;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-detail">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $nama; ?></h5>
            <button type="button" class="btn-close close tutup" id="<?php echo $id; ?>"></button>
        </div>
        <div class="modal-body">
          <?php
          //jika siswa sudah mengerjakan tampilkan hasil
          if(current_user_can('mahasiswa')){
            $sudahjawab 	= $wpdb->get_results("SELECT * FROM $table_quiz WHERE tipe = 'jawab' and tujuan = $id and iduser = $user_id");
            if ($sudahjawab) {
                $datajawab 	= $sudahjawab[0];
                $detailc 		= $datajawab->detail;
                $detail 		= json_decode($detailc);
                $jml_soal   = count((array)$detail->jawaban);
                echo '<div class="card mx-auto mb-4">
                      <div class="card-body text-center bg-nilai">
                        <h5 class="card-title">Nilai anda : </h5>
                        <p class="card-text h1" style="font-size: 7rem;">'.$detail->nilai.'</p>
                      </div>
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fa fa-check text-success"></i> Benar = '.$detail->benar.'</li>
                        <li class="list-group-item"><i class="fa fa-close text-danger"></i> Salah = '.$detail->salah.'</li>
                        <li class="list-group-item"><i class="fa fa-question-circle text-warning"></i> Tidak dijawab = '.$detail->tidakdijawab.'</li>
                        <li class="list-group-item"><i class="fa fa-wpforms"></i> Jumlah Soal = '.$jml_soal.'</li>
                        <li class="list-group-item"><i class="fa fa-calendar text-dark"></i> Dikerjakan Pada = '.date("d-m-Y", strtotime($detail->waktuawal)).'</li>
                        <li class="list-group-item"><i class="fa fa-clock-o text-dark"></i> Jam = '.date("H:i:s", strtotime($detail->waktuawal)).' - '.date("H:i:s", strtotime($detail->waktuakhir)).'</li>
                      </ul>
                    </div>';
                  echo '<div class="h6 m-1 font-weight-bold"> Detail Quiz </div>';
            }
          } else {
            echo '<div class="alert alert-info" role="alert"> Total Dikerjakan : <strong>'.$dikerjakan.'</strong></div>';
          }
          ?>

            <table class="table"><tbody>
            <tr><td style="border: 0;">Dosen</td><td style="border: 0;">:</td><td style="border: 0;"><?php echo get_userdata($iduserc)->first_name; ?> </td></tr>
            <tr><td>Catatan</td><td>:</td><td><?php echo $catatan; ?></td></tr>
            <tr><td>Mata Kuliah</td><td>:</td><td><?php echo $data_makul->nama_makul; ?></td></tr>
            <tr><td>Kelas</td><td>:</td><td><ul class="pl-3">
              <?php
              foreach ( $idkelas as $kelas ) {
                echo '<li>'.$kelas.'</li>';
              }
              ?>
            </ul></td></tr>
            <tr><td>Tanggal</td><td>:</td><td><?php echo $tanggal; ?></td></tr>
            <tr><td>Waktu</td><td>:</td><td><?php echo $waktu; ?></td></tr>

            </tbody></table>

        </div>
        <div class="modal-footer text-right d-block">
              <?php
              //jika yang login mahasiswa
              if(current_user_can('mahasiswa')){
                  $sudahjawab 	= $wpdb->get_results("SELECT * FROM $table_quiz WHERE tipe = 'jawab' and tujuan = $id and iduser = $user_id");
                  if ($sudahjawab) {
                    echo '<button class="btn btn-success my-1">Sudah Dikerjakan</button>';
                  } else {
                    echo '<a href="?halaman=quiz&aksi=tampil&id='.$id.'" class="btn btn-info my-1">Kerjakan</a>';
                  }
              } else {
                  echo '<a href="?halaman=quiz&list=jawaban&id='.$id.'" class="btn btn-info my-1">Daftar Jawaban</a>';
                  echo '<a href="?halaman=quiz&aksi=tampil&id='.$id.'" class="btn btn-success my-1">Pratinjau</a>';
                  echo '<a href="?halaman=quiz&aksi=edit&id='.$id.'" class="btn btn-info my-1">Edit</a>';
              }
              ?>
              <button type="button" class="btn btn-secondary tutup my-1" id="<?php echo $id; ?>">Tutup</button>
          </div>
        </div>
      </div>
    </div>