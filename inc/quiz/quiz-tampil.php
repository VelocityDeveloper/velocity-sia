<?php
global $wpdb;
$sesieror     = '';
$user_id 			= get_current_user_id();
$id_semester 	= get_user_meta($user_id,'semester',true);
$thn_ajaran 	= get_user_meta($user_id,'angkatan',true);
$prodiku 			= get_user_meta($user_id,'id_prodi',true);
$kelasku 			= get_user_meta($user_id,'kelas',true);
$date		 			= date( 'd-m-Y H:i:s', current_time( 'timestamp', 0 ) );
$id 					= isset($_GET['id'])? $_GET['id'] : '';
$table_quiz 	= $wpdb->prefix . "v_quiz";
$table_makul 	= $wpdb->prefix . "v_mata_kuliah";
$table_krs 		= $wpdb->prefix . "v_krs";
$table_jadwal = $wpdb->prefix . "v_jadwal";
$tampil_quiz  = $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
$data 				= $tampil_quiz[0];
$detailc 			= json_decode($data->detail);
$nama					= $detailc->nama;
$waktu			  = $detailc->waktu;

  //jika siswa login
  if(current_user_can('mahasiswa')){
    $sudahjawab 	= $wpdb->get_results("SELECT * FROM $table_quiz WHERE tipe = 'jawab' and tujuan = $id and iduser = $user_id");
    if ($sudahjawab) {
        $sesieror  .= '<div class="alert alert-info" role="alert"> Sudah Anda Kerjakan </div>';
        unset($_SESSION['kerjaquiz']);
    }
  }

  //dapatkan urutan soal
  foreach ($detailc->pertanyaan as $idsoal => $value) { $map[] = array( 'idsoal' => $value->idsoal ); }

 
  //set waktu
  if (empty($_SESSION['kerjaquiz']['setwaktuawal']) && $waktu) {
      $_SESSION['kerjaquiz']['setwaktuawal'] = $date;
      $endTime = strtotime("+".$waktu." minutes", strtotime($date));
      $expTime = date('Y/m/d H:i:s', $endTime);
  } elseif ($waktu) {
      $to_time    = strtotime($date);
      $from_time  = strtotime($_SESSION['kerjaquiz']['setwaktuawal']);
      $bettime    = $to_time - $from_time;
      $newmin     = $waktu*60;
      $newminute  = ceil($newmin-$bettime);
      $endTime    = strtotime("+".$newminute." seconds", strtotime($date));
      $expTime    = date('Y/m/d H:i:s', $endTime);
  }

 // echo '<pre>'.print_r($detailc,1).'</pre>';

if (empty($sesieror)) { ?>

<p id="countdown9"></p>

<div id="mainform">

<p id="countdown8" class="text-center mb-4"></p>

<form id="formquiz" class="quizet card" name="inputquiz" method="POST">
  <input type="hidden" name="idquiz" value="<?php echo $id ; ?>">
  <input type="hidden" name="iduser" value="<?php echo $user_id ; ?>">
  <input type="hidden" name="jumlahsoal" value="<?php echo count($map) ; ?>">
  <div class="card-header h5"><?php echo $nama	; ?></div>
  <div class="card-body mb-4">
          <?php
          foreach ($detailc->pertanyaan as $idsoal => $value) {
            $urutx = array_search($idsoal, array_column($map, 'idsoal'))+1;
            $uruty = array_search($idsoal, array_column($map, 'idsoal'))-1;
            $soale = str_replace("\\", '', $value->soal);

            if ($urutx == 1) { $classa = 'show'; } else { $classa = ''; }

            echo '<div id="coll-'.$idsoal.'" class="'.$classa.' collapse kolomsoal">';

              echo '<div class="h6 font-weight-bold mb-3"> Soal '.$urutx.' </div>';
              echo '<div class="card p-3 border border-success"> '.$soale.' </div>';

              echo '<div class="mt-4">';
              $arra = array('a' => 'A','b' => 'B','c' => 'C','d' => 'D');
              foreach ($arra as $keya => $keyb) {
                if ((isset($_SESSION['kerjaquiz']['jawab'][$idsoal])) && ($_SESSION['kerjaquiz']['jawab'][$idsoal] == $keya)) {
                  $cekchecked = 'checked';
                } else {
                  $cekchecked = "";
                }
                echo '
                <div class="d-block">
                <input class="d-none soalradio" type="radio" data-id="'.$idsoal.'" id="soal-'.$idsoal.''.$keya.'" name="'.$idsoal.'" value="'.$keya.'" '.$cekchecked.'>
                <label class="col m-0 p-0" for="soal-'.$idsoal.''.$keya.'">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <div class="input-group-text radio-elv"> '.$keyb.'</div>
                        </div>
                        <div class="form-control radio-elv">'.$value->$keya.'</div>
                      </div>
                </label>
                </div>';
              }
              echo '</div>';

              if (isset($map[$uruty])) {
                $urutbefore = $map[$uruty]['idsoal'];
                echo '<a class="my-3 me-3 btn btn-dark linksoal text-white" id="'.$urutbefore.'"><i class="fa fa-caret-left"></i> Sebelumnya</a>';
              }
              if (isset($map[$urutx])) {
                $urutnext = $map[$urutx]['idsoal'];
                echo '<a class="my-3 btn btn-dark linksoal text-white" id="'.$urutnext.'">Selanjutnya <i class="fa fa-caret-right"></i></a>';
              }


            echo '</div>';
            }
            ?>
  </div>
  <div class="card-footer">
    <nav aria-label="Page navigation" class="overflowa">
      <ul class="pagination">
        <?php
        foreach ($detailc->pertanyaan as $idsoal => $value) {
          if (isset($_SESSION['kerjaquiz']['jawab'][$idsoal])) { $cekclass = "bg-success text-white";} else {$cekclass = "";}
          $urut = array_search($idsoal, array_column($map, 'idsoal'))+1;
          echo '<li class="page-item"><a class="page-link linksoal link'.$idsoal.' '.$cekclass.'" id="'.$idsoal.'">'.$urut.'</a></li>';
        }
        ?>
      </ul>
    </nav>
    <hr>
    <a class="btn btn-primary btn-lg btn-block selesai text-white" id-quiz="<?php echo $id; ?>" id-user="<?php echo $user_id; ?>">Selesai</a>
  </div>
</form>
</div>

<script>
jQuery(document).ready(function($){
  $.fn.serializeObject = function() {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
    if (o[this.name]) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || '');
    } else {
      o[this.name] = this.value || '';
    }
  });
  return o;
};
var reset = function(){
    $(".input-field").val('');
}
  $(document).on("click",".linksoal",function(e){
    var get_id = $(this).attr("id");
    $(".kolomsoal").removeClass("show");
    $("#coll-" + get_id).toggleClass("show");
  });
  $(document).on("click",".soalradio",function(e){
    var get_id = $(this).attr("data-id");
    var value  = $(this).attr("value");
      $.ajax({
  			type: "POST",
  			data: "action=jawabsoal&id=" + get_id +"&value=" + value,
  			url: sia_ajaxurl,
  			success:function(data) {
  				$(".link" + get_id).addClass("bg-success text-white");
  			}
  		});
  });
  $(document).on("click",".selesai",function(e){
      var detailform = $('#formquiz').serializeObject();
      $("#mainform").html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
      jQuery.ajax({
              type: "POST",
              url: sia_ajaxurl,
              data: {action:'inputquiz', detail:detailform },
              success:function(data) {
                  $('#mainform').html(data);
          },
      });
      e.preventDefault();
  });
  <?php if ($waktu) { ?>
  $('#countdown8').countdown('<?php echo $expTime; ?>')
  .on('update.countdown', function(event) {
    var format = '<div class="btn btn-secondary">%H</div> : <div class="btn btn-secondary">%M</div> : <div class="btn btn-secondary">%S</div>';
    if(event.offset.totalDays > 0) {
      format = '<div class="btn btn-secondary">%-d Hari </div> - ' + format;
    }
    if(event.offset.weeks > 0) {
      format ='<div class="btn btn-secondary"> %-w Minggu </div> ' + format;
    }
    $(this).html(event.strftime(format));
  })
  .on('finish.countdown', function(event) {
      $("#countdown9").html('<div class="mb-4"><div class="alert alert-danger mx-auto">Waktu Habis</div></div>');
        var detailform = $('#formquiz').serializeObject();
        $("#mainform").html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
        jQuery.ajax({
                type: "POST",
                url: sia_ajaxurl,
                data: {action:'inputquiz', detail:detailform },
                success:function(data) {
                    $('#mainform').html(data);
            },
        });
        e.preventDefault();
  });
  <?php } ?>
});
</script>

<?php } else {
    echo $sesieror;
}
