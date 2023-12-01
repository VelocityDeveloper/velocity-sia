<?php
$user_id = get_current_user_id();
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_quiz 	= $wpdb->prefix . "v_quiz";
$table_makul 	= $wpdb->prefix . "v_mata_kuliah";
$table_Kelas 	= $wpdb->prefix . "v_kelas";

$aksi = isset($_GET['aksi'])? $_GET['aksi'] : '';
$id 	= isset($_GET['id'])? $_GET['id'] : '' ;
$is 	= isset($_GET['is'])? $_GET['is'] : '' ;
$ids 	= isset($_GET['ids'])? $_GET['ids'] : '' ;

$datex 		= current_time( 'mysql' );
$datenow 	= date("d-m-Y H:i:s", strtotime($datex));

if(current_user_can('administrator')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul");
} if(current_user_can('dosen')){
	$tampil_makul = $wpdb->get_results("SELECT * FROM $table_makul WHERE id_dosen = $user_id");
}
$tampil_kelas = $wpdb->get_results("SELECT * FROM $table_Kelas");

$sesierrors = '';

$get_data 	= $wpdb->get_results("SELECT * FROM $table_quiz WHERE id = $id");
if (empty($get_data)) {
      $sesierrors .= '<div class="alert alert-danger" role="alert">Maaf anda tidak dapat melihat halaman ini </div>
      <a class="btn btn-secondary btn-sm" href="?halaman=quiz">Kembali ke Halaman Quiz</a>';
}


//check jika tidak ada eror
if (empty($sesierrors)) {
      $cekerrors  = '';
      $datac 		= $get_data[0];
      $iduserc		= $datac->iduser;
      $detailc 		= json_decode($datac->detail);
      $tujuanc 		= json_decode($datac->tujuan);


      //jika login sebagai user, check id user
      if(current_user_can('dosen') || current_user_can('mahasiswa')){
          if ($iduserc != $user_id) {
            $cekerrors .= '<div class="alert alert-danger" role="alert">Maaf anda tidak dapat melihat halaman ini </div>
            <a class="btn btn-secondary btn-sm" href="?halaman=quiz">Kembali ke Halaman Quiz</a>';
          }
      }

      if (isset($_SESSION['editquiz'])) {
          if ($_SESSION['editquiz']['setting']['idquiz'] != $id) {
              unset($_SESSION['editquiz']);
          }
      }

      //jika reset
      if ((isset($_POST['reset']) == "reset")) {
      		unset($_SESSION['editquiz']);
      		echo '<div class="alert alert-success" role="alert">Data Form berhasil diatur ulang</div>';
      		echo '<script>window.setTimeout(function(){
      						window.location.href = "?halaman=quiz";
      					},0);</script>';
      }


      ///tambah data ke session
      if ((isset($_POST['act']) == "tambah") && ($aksi=='edit')) {
      		if ((empty($_SESSION['editquiz']['setting'])) || ($is == 'awal')) {
      			$detail = array(
      						"nama" 				=> $_POST['nama'],
      						"catatan" 		=> $_POST['catatan'],
      						"waktu" 			=> $_POST['waktu'],
      			);
      			$tujuan = array(
      						"mata_kuliah" => $_POST['mata_kuliah'],
      						"kelas"				=> $_POST['kelas'],
      			);
      			$_SESSION['editquiz']['setting']= array(
      					 'detail'    => $detail,
      					 'tujuan'    => $tujuan,
                 "iduser" 	 => $datac->iduser,
                 "idquiz" 	 => $datac->id,
      			);
      			if (empty($is)) {
      				$_SESSION['editquiz']['count'] = 1;
      				$_SESSION['editquiz']['pertanyaan'] = [];
      			} else {
      				echo '<script>window.setTimeout(function(){window.location.href = "?halaman=quiz&aksi=edit&id='.$id.'";},0);</script>';
      			}
      		} else {
      			$_SESSION['editquiz']['pertanyaan'][$_POST['idsoal']] = array(
      					 'soal'   => $_POST['soal'],
      					 'a'    	=> $_POST['a'],
      					 'b'    	=> $_POST['b'],
      					 'c'    	=> $_POST['c'],
      					 'd'    	=> $_POST['d'],
      					 'benar' 	=> $_POST['jawaban'],
      					 'idsoal' => $_POST['idsoal'],
      			);
      			if (empty($is) && empty($ids)) {
      				$_SESSION['editquiz']['count'] = $_POST['idsoal']+1;
      			}
      			if ((isset($_POST['update']))) {
      				echo '<script>window.setTimeout(function(){window.location.href = "?halaman=quiz&aksi=edit&id='.$id.'";},0);</script>';
      			}
      		}
      }

      if (($aksi=='edit') && (!isset($_SESSION['editquiz'])) && (!isset($_POST['reset'])) && (empty($cekerrors)) && (!isset($_POST['simpanquiz']))) {
      		$detail = array(
      					"nama" 				=> $detailc->nama,
      					"catatan" 		=> $detailc->catatan,
      					"waktu" 			=> $detailc->waktu,
      		);
      		$tujuan = array(
      					"mata_kuliah" => $tujuanc->mata_kuliah,
      					"kelas"				=> $tujuanc->kelas,
      		);
      		$_SESSION['editquiz']['setting']= array(
      				 'detail'     => $detail,
      				 'tujuan'     => $tujuan,
               "iduser" 	  => $datac->iduser,
               "idquiz" 	  => $datac->id,
      		);
          foreach ($detailc->pertanyaan as $key => $value) {
            $_SESSION['editquiz']['pertanyaan'][$key] = array(
      					 'soal'   => $value->soal,
      					 'a'    	=> $value->a,
      					 'b'    	=> $value->b,
      					 'c'    	=> $value->c,
      					 'd'    	=> $value->d,
      					 'benar' 	=> $value->benar,
      					 'idsoal' => $value->idsoal,
      			);
          }
		  $question = $_SESSION['editquiz']['pertanyaan'];
          $arrx = $question ? end($_SESSION['editquiz']['pertanyaan']):'';
          $_SESSION['editquiz']['count'] = $arrx ? $arrx['idsoal']+1 : 0;
      }

      //simpan data dari session ke database
      if ((isset($_POST['simpanquiz']) == "simpanquiz") && ($aksi=='edit')) {
          $detail = array(
                "nama" 				=> $_SESSION['editquiz']['setting']['detail']['nama'],
                "catatan" 		=> $_SESSION['editquiz']['setting']['detail']['catatan'],
                "waktu" 			=> $_SESSION['editquiz']['setting']['detail']['waktu'],
                "pertanyaan" 	=> $_SESSION['editquiz']['pertanyaan'],
          );
          $tujuan = array(
                "mata_kuliah" => $_SESSION['editquiz']['setting']['tujuan']['mata_kuliah'],
                "kelas"				=> $_SESSION['editquiz']['setting']['tujuan']['kelas'],
          );
          $wpdb->update( $table_quiz, array(
              'tipe' 		=> $_POST['tipe'],
              'tanggal' => $datenow,
              'iduser' 	=> $_SESSION['editquiz']['setting']['iduser'],
              'tujuan' 	=> json_encode($tujuan),
              'detail' 	=> json_encode($detail),
          ), array('id'=> $id));

          //hapus session
          unset($_SESSION['editquiz']);
          echo '<div class="alert alert-success" role="alert">Quiz berhasil disimpan</div>';
          echo '<script>window.setTimeout(function(){
                  window.location.href = "?halaman=quiz";
                }, 500);</script>';
      }

      if ($is == 'awal') {
      		$nama 		= $_SESSION['editquiz']['setting']['detail']['nama'];
      		$catatan 	= $_SESSION['editquiz']['setting']['detail']['catatan'];
      		$idmatkul = $_SESSION['editquiz']['setting']['tujuan']['mata_kuliah'];
      		$idkelas 	= $_SESSION['editquiz']['setting']['tujuan']['kelas'];
      		$waktu	 	= $_SESSION['editquiz']['setting']['detail']['waktu'];
      }
      if (!empty($ids) && !empty($_SESSION['editquiz']['pertanyaan'])) {
					$soalx 		= str_replace("\\", '', $_SESSION['editquiz']['pertanyaan'][$ids]['soal']);
					$soal 		= $soalx;
      		$a 				= $_SESSION['editquiz']['pertanyaan'][$ids]['a'];
      		$b 				= $_SESSION['editquiz']['pertanyaan'][$ids]['b'];
      		$c 				= $_SESSION['editquiz']['pertanyaan'][$ids]['c'];
      		$d 				= $_SESSION['editquiz']['pertanyaan'][$ids]['d'];
      		$benar		= $_SESSION['editquiz']['pertanyaan'][$ids]['benar'];
      }

  // cek eror
  if (empty($cekerrors)) {
?>

        <div class="card">
        		<div class="card-header bg-info text-white font-weight-bold h6">
        			Edit Quiz
        			<?php if (isset($_SESSION['editquiz'])) { ?>
        				<div class="dropdown pull-right">
        				  <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        				    Navigasi
        				  </button>
        				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        				    <a class="dropdown-item" href="?halaman=quiz&aksi=edit&id=<?php echo $id; ?>&is=awal">Pengaturan Dasar</a>
        							<?php
        							if ((isset($_SESSION['editquiz']['pertanyaan'])) && (!empty($_SESSION['editquiz']['pertanyaan']))) {
        								$navN = 1;
        								foreach ($_SESSION['editquiz']['pertanyaan'] as $key => $value) {
        									echo '<a class="dropdown-item" href="?halaman=quiz&aksi=edit&id='.$id.'&ids='.$key.'">Soal '.$navN++.'</a>';
        								}
        							}
        							?>
        				  </div>
        				</div>
        		<?php } ?>
        		</div>

        		<div class="card-body pb-5">
        			<form name="input" method="POST" enctype="multipart/form-data">

        				<?php
        				if ((!isset($_SESSION['editquiz'])) || ($is == 'awal')) {
        					require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz-template-dasar.php' );
        				} else {
        					require_once ( VELOCITY_SIA_PLUGIN_DIR.  '/inc/quiz/quiz-template-soal.php' );
        				}

                	echo '<input type="hidden" name="act" value="tambah" />';
          				if ($is || $ids) {
          					echo '<input type="hidden" name="idsoal" value="'.$ids.'">';
          					echo '<input type="hidden" name="update" value="'.$ids.'">';
          				} else {
          					if ((isset($_SESSION['editquiz']['count']))) {
          						echo '<input type="hidden" name="idsoal" value="'.$_SESSION['editquiz']['count'].'" />';
          					}
          				}
          				echo '<input type="submit" name="submit" class="btn btn-info mt-3 mb-4" value="Simpan & Tambah Soal">';
          			?>

        			</form>
        			<hr>
        			<div class="row mt-5">
        				<form name="input" method="POST" class="col">
        						<input type="hidden" name="reset" value="reset" />
        						<input class="btn btn-dark w-100 btn-block" type="submit" value="Batal">
        				</form>
        				<div class="col">
        						<button type="button" class="btn btn-primary w-100 btn-block" data-bs-toggle="modal" data-bs-target="#soalsimpanModal">Simpan</button>
        				</div>
        			</div>

        	</div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="soalsimpanModal" tabindex="-1" role="dialog" aria-labelledby="soalsimpanModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form name="input" method="POST" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="soalsimpanModalLabel">Simpan Quiz?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
        				<div class="alert alert-warning" role="alert">
        					<i class="fa fa-info-circle"></i> Pastikan anda sudah klik "Simpan & Tambah Soal" pada tiap soal
        				</div>
        				<div class="form-group">
        					<select class="form-control" name="tipe">
        						<option value="publish">Langsung terbitkan</option>
        				    	<option value="draft">Simpan sebagai konsep</option>
        				    </select>
        					<input type="hidden" name="simpanquiz" value="simpanquiz" />
        				</div>
              </div>
              <div class="modal-footer">
                <a class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</a>
        		<input type="submit" name="submit" class="btn btn-primary" value="Simpan">
              </div>
            </form>
          </div>
        </div>

        <script>
        jQuery(document).ready(function ($) {
        	$(document).on("click",".hapussoal",function(e){
        		var get_id = $(this).attr("id");
        		$.ajax({
        			type: "POST",
        			data: "action=hapussoal&idsoal=" + get_id,
        			url: sia_ajaxurl,
        			success:function(data) {
        				window.setTimeout(function(){
        					window.location.href = "?halaman=quiz&aksi=edit&id=<?php echo $id; ?>";
        				}, 500);
        			}
        		});
        	});
        });
        </script>

<?php
  } else {
  	echo $cekerrors;
  }
} else {
	echo $sesierrors;
} ?>