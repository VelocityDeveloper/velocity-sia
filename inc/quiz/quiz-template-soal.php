<?php
  if ($ids) {
    if ($aksi=='tambah') {
      $urut = array_search($ids, array_column($_SESSION['buatquiz']['pertanyaan'], 'idsoal'))+1;
    }
    if ($aksi=='edit') {
      $urut = array_search($ids, array_column($_SESSION['editquiz']['pertanyaan'], 'idsoal'))+1;
    }
  } else {
    if ($aksi=='tambah') {
      if ($_SESSION['buatquiz']['pertanyaan']) {
        $urut = count($_SESSION['buatquiz']['pertanyaan'])+1;
      } else {
        $urut = count($_SESSION['buatquiz']['pertanyaan'])!=0?count($_SESSION['buatquiz']['pertanyaan']):1;
      }
    }
    if ($aksi=='edit') {
      if ($_SESSION['editquiz']['pertanyaan']) {
        $urut = count($_SESSION['editquiz']['pertanyaan'])+1;
      } else {
        $urut = count($_SESSION['editquiz']['pertanyaan'])!=0?count($_SESSION['editquiz']['pertanyaan']):1;
      }
    }
  }
 ?>

      <?php  if ($ids) { ?>
          <a id="<?php echo $urut;?>" class="hapussoal btn btn-sm btn-danger text-white mb-4"><i class="fa fa-trash" aria-hidden="true"></i> Hapus</a>
      <?php } ?>

      <h5 class="font-weight-bold mb-4"><?php  if ($ids) { echo 'Edit '; }?>Soal <?php echo $urut;?></h5>

      <div class="form-group">
        <div class="mb-2">
          <?php
            if (isset($soal)) {
              $konten = $soal;
            } else {
              $konten = '';
            }
            $sett = array(
              'media_buttons' => false,
              'tinymce'       => array(
                      'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                      'toolbar2'      => '',
                      'toolbar3'      => '',
                  ),
               );
            $editor_id = 'soal';
            wp_editor( $konten, $editor_id,$sett);
          ?>
        </div>
      </div>
      <h6>Pilihan Jawaban</h6>
        <div class="input-group mb-2">
          <div class="input-group-prepend"><div class="input-group-text">A</div></div>
          <input type="text" class="form-control" name="a" value="<?php if (isset($a)) { echo $a; }?>" required>
        </div>
        <div class="input-group mb-2">
          <div class="input-group-prepend"><div class="input-group-text">B</div></div>
          <input type="text" class="form-control" name="b" value="<?php if (isset($b)) { echo $b; }?>" required>
        </div>
        <div class="input-group mb-2">
          <div class="input-group-prepend"><div class="input-group-text">C</div></div>
          <input type="text" class="form-control" name="c" value="<?php if (isset($c)) { echo $c; }?>" required>
        </div>
        <div class="input-group mb-2">
          <div class="input-group-prepend"><div class="input-group-text">D</div></div>
          <input type="text" class="form-control" name="d" value="<?php if (isset($d)) { echo $d; }?>" required>
        </div>

        <div class="form-group">
          <label for="jawaban" class="mt-3 h6">Jawaban Benar</label>
            <select class="form-control" name="jawaban" required>
              <?php
              $arra = array('a' => 'A','b' => 'B','c' => 'C','d' => 'D');
              foreach ( $arra as $arr => $arrx ) { ?>
                <option value="<?php echo $arr; ?>" <?php if ((isset($benar))&&($benar == $arr)){echo 'selected';};?>><?php echo $arrx; ?></option>
              <?php } ?>
            </select>
        </div>
