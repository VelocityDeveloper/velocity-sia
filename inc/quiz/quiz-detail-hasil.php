<?php
global $wpdb;
$id 					= isset($_POST['id'])?$_POST['id'] : '';
$table_quiz 	= $wpdb->prefix . "v_quiz";
$getdatajawab = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
$datajawab 	  = $getdatajawab[0];
$iduser 		  = $datajawab->iduser;
$detailjc		  = $datajawab->detail;
$idsoal 		  = $datajawab->tujuan;
$detail 		  = json_decode($detailjc);
$jawaban 			= $detail->jawaban;

//data quiz
$tampil_quiz  = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $idsoal");
$data 				= $tampil_quiz[0];
$detailk 			= $data->detail;
$detailq 			= json_decode($detailk);
//dapatkan urutan soal
foreach ($detailq->pertanyaan as $idsoal => $value) { $map[] = array( 'idsoal' => $value->idsoal ); }

?>

  <div id="mdl-<?php echo $id; ?>" class="modal fade show" style="padding-right: 15px;background: #0006;display: block;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-detail">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo get_userdata($iduser)->first_name; ?></h5>
            <button type="button" class="btn-close close tutup" id="<?php echo $id; ?>"></button>
        </div>
        <div class="modal-body">
          <?
          echo '<div id="tabs1" class="card mx-auto mb-4 kolomtabs">
                <div class="card-body text-center bg-nilai">
                  <h5 class="card-title">Nilai : </h5>
                  <p class="card-text h1" style="font-size: 7rem;">'.$detail->nilai.'</p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item"><i class="fa fa-check text-success"></i> Benar = '.$detail->benar.'</li>
                  <li class="list-group-item"><i class="fa fa-close text-danger"></i> Salah = '.$detail->salah.'</li>
                  <li class="list-group-item"><i class="fa fa-question-circle text-warning"></i> Tidak dijawab = '.$detail->tidakdijawab.'</li>
                  <li class="list-group-item"><i class="fa fa-wpforms"></i> Jumlah Soal = '.count($detail->jawaban).'</li>
                  <li class="list-group-item"><i class="fa fa-calendar text-dark"></i> Dikerjakan Pada = '.date("d-m-Y", strtotime($detail->waktuawal)).'</li>
                  <li class="list-group-item"><i class="fa fa-clock-o text-dark"></i> Jam = '.date("H:i:s", strtotime($detail->waktuawal)).' - '.date("H:i:s", strtotime($detail->waktuakhir)).'</li>
                </ul>
              </div>';
              echo '<div class="h6 font-weight-bold">Detail</div>';
              echo '<div class="table-responsive"><table class="table table-hover">';
              echo '<thead><tr>
                      <th scope="col">No</th>
                      <th scope="col">Jawaban</th>
                      <th scope="col">Hasil</th>
                    </tr></thead><tbody>';
              foreach ($detailq->pertanyaan as $idsoal => $value) {
                $urutx = array_search($idsoal, array_column($map, 'idsoal'))+1;
                if ($value->benar == $jawaban->$idsoal->$idsoal) {
                  $classx = '';
                  $hasilx = 'Benar';
                } else {
                  $classx = 'alert alert-danger';
                  $hasilx = 'Salah';
                }
                echo '<tr class="'.$classx.'"><td>'.$urutx.'</td><td>'.$jawaban->$idsoal->$idsoal.'</td><td>'.$hasilx.'</td></tr>';
              }
              echo '</tbody></table></div>';

          ?>
        </div>
        <div class="modal-footer text-right d-block">
              <a href="?halaman=quiz&list=detail&id=<?php echo $id; ?>" class="btn btn-info tutup my-1" >Detail</a>
              <button type="button" class="btn btn-secondary tutup my-1" id="<?php echo $id; ?>">Tutup</button>
          </div>
        </div>
      </div>
    </div>
