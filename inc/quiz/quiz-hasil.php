<?
global $wpdb;
$id           = isset($_GET['id'])? $_GET['id'] : '';
$table_quiz   = $wpdb->prefix . "v_quiz";
//jawaban
$tampil_jawab = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
$dataj 				= get_object_vars($tampil_jawab[0]);
$idsoal 			= $dataj['tujuan'];
$iduser 			= $dataj['iduser'];
$detailt 			= $dataj['detail'];
$detailj 			= json_decode($detailt);
$jawaban 			= $detailj->jawaban;
///quiz
$tampil_quiz  = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $idsoal");
$data 				= get_object_vars($tampil_quiz[0]);
$detailk 			= $data['detail'];
$detailq 			= json_decode($detailk);
$nama					= $detailq->nama;
//dapatkan urutan soal
foreach ($detailq->pertanyaan as $idsoal => $value) { $map[] = array( 'idsoal' => $value->idsoal ); }

echo '<div id="tabs1" class="card mx-auto mb-4 kolomtabs">
      <ul class="list-group list-group-flush">
        <li class="list-group-item"><i class="fa fa-user"></i> Nama = '.get_userdata($iduser)->first_name.'</li>
        <li class="list-group-item"><i class="fa fa-asterisk text-success"></i> Nilai = '.$detailj->nilai.'</li>
        <li class="list-group-item"><i class="fa fa-check text-success"></i> Benar = '.$detailj->benar.'</li>
        <li class="list-group-item"><i class="fa fa-close text-danger"></i> Salah = '.$detailj->salah.'</li>
        <li class="list-group-item"><i class="fa fa-question-circle text-warning"></i> Tidak dijawab = '.$detailj->tidakdijawab.'</li>
        <li class="list-group-item"><i class="fa fa-wpforms"></i> Jumlah Soal = '.count($detailj->jawaban).'</li>
        <li class="list-group-item"><i class="fa fa-calendar text-dark"></i> Dikerjakan Pada = '.date("d-m-Y", strtotime($detailj->waktuawal)).'</li>
        <li class="list-group-item"><i class="fa fa-clock-o text-dark"></i> Jam = '.date("H:i:s", strtotime($detailj->waktuawal)).' - '.date("H:i:s", strtotime($detailj->waktuakhir)).'</li>
      </ul>
    </div>';
          echo '<div class="h6 font-weight-bold">Detail</div>';
          echo '<div class="table-responsive"><table class="table table-hover">';
          echo '<thead><tr>
                  <th scope="col">No</th>
                  <th scope="col">Jawaban</th>
                  <th scope="col">Hasil</th>
                  <th scope="col" style="width: 95px;">Detail</th>
                </tr></thead><tbody>';
          foreach ($detailq->pertanyaan as $idsoal => $value) {
            $urutx   = array_search($idsoal, array_column($map, 'idsoal'))+1;
            if ($value->benar == $jawaban->$idsoal->$idsoal) {
              $classx = '';$hasilx = 'Benar';
            } else {
              $classx = 'alert alert-danger'; $hasilx = 'Salah';
            }
            echo '<tr class="'.$classx.'">
                  <td>'.$urutx.'</td>
                  <td>'.$jawaban->$idsoal->$idsoal.'</td>
                  <td>'.$hasilx.'</td>
                  <td><a class="btn btn-primary btn-sm text-white" data-bs-toggle="collapse" href="#coll'.$value->idsoal.'" role="button" aria-expanded="false" aria-controls="coll'.$value->idsoal.'"><i class="fa fa-eye"></i></a></td>
                  </tr>';
            echo '<tr class="collapse bg-info2" id="coll'.$value->idsoal.'">';
            echo '<td colspan="4"><div class="d-block">'.$value->soal.'</div>';
            $arra = array('a' => 'A','b' => 'B','c' => 'C','d' => 'D');
            foreach ($arra as $keya => $keyb) {
                echo '<div class="d-block">'.$keyb.'.'.$value->$keya.'</div>';
            }
            echo '<div class="d-block my-3">Jawaban = '.$jawaban->$idsoal->$idsoal.' <br> Jawaban Benar = '.$value->benar.'</div></td>
                  </tr>';
          }
          echo '</tbody></table></div>';
            ?>


<script>
jQuery(document).ready(function($){
  $(document).on("click",".linksoal",function(e){
    var get_id = $(this).attr("id");
    $(".kolomsoal").removeClass("show");
    $("#coll-" + get_id).toggleClass("show");
  });
});
</script>
