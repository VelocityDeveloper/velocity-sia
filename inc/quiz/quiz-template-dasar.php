
    <div class="form-group mb-4">
      <label class="mb-1" for="nama" class="font-weight-bold">Nama Quiz</label>
      <input required type="text" class="form-control" name="nama" value="<?php if (isset($nama)) { echo $nama; }?>" id="nama" placeholder="Nama Quiz" required />
    </div>
    <div class="form-group mb-3">
      <label class="mb-1" for="catatan" class="font-weight-bold">Catatan</label>
      <div class="mb-2">
      <?php
        if (isset($catatan)) {
          $content = $catatan;
        } else {
          $content = '';
        }
        $settings  = array(
          'media_buttons' => false,
          'tinymce'       => array(
                  'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                  'toolbar2'      => '',
                  'toolbar3'      => '',
              ),
           );
        $editor_id = 'catatan';
        wp_editor( $content, $editor_id,$settings);
      ?>
      </div>
    </div>
    <div class="form-group mb-3">
      <label class="mb-1" for="mata_kuliah" class="font-weight-bold">Mata Kuliah</label>
      <select class="form-control" name="mata_kuliah" required>
        <option value="">Pilih Mata Kuliah</option>
        <?php foreach ( $tampil_makul as $matkul ) { ?>
          <option value="<?php echo $matkul->id_makul; ?>" <?php if ((isset($idmatkul))&&($matkul->id_makul == $idmatkul)){echo 'selected';};?>><?php echo $matkul->nama_makul; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group mb-3">
      <label class="mb-1" for="kelas" class="font-weight-bold">Kelas</label>
      <div class="form-control">
        <?php foreach ( $tampil_kelas as $kelas ) { ?>
          <div class="checkbox">
            <label><input name="kelas[]" type="checkbox" value="<?php echo $kelas->nama; ?>" <?php if (isset($idkelas)){ if (in_array($kelas->nama, $idkelas)) {echo 'checked';}};?>> <?php echo $kelas->nama; ?> </label>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="form-group mb-4">
      <label class="mb-1" for="waktu" class="font-weight-bold">Waktu Pengerjaan</label>
      <input type="number" class="form-control" name="waktu" value="<?php if (isset($waktu)) { echo $waktu; }?>" id="waktu" placeholder="" />
      <small>dalam menit. Kosongkan jika tanpa batas waktu pengerjaan</small>
    </div>
