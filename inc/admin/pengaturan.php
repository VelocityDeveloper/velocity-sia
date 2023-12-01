<?php
$array_option = array (
		array('title' => 'Ijinkan Dosen mengedit profil', 'id' => 'profil_dosen'),
		array('title' => 'Ijinkan Dosen mengedit Foto profil', 'id' => 'foto_profil_dosen'),
		array('title' => 'Ijinkan Dosen mengubah Password', 'id' => 'akun_profil_dosen'),
		array('title' => 'Ijinkan Mahasiswa mengedit profil', 'id' => 'profil_mahasiswa'),
		array('title' => 'Ijinkan Mahasiswa mengedit Foto profil', 'id' => 'foto_profil_mahasiswa'),
		array('title' => 'Ijinkan Mahasiswa mengubah Password', 'id' => 'akun_profil_mahasiswa'),
);

$idlogo_elv = get_option( 'logo_elv');
$logo_elv = isset($_POST['logo_elv']) ? $_POST['logo_elv'] : '';
if(isset($_POST['logo_elv']) || isset($_POST['submit'])){
	if(empty($_POST['logo_elv'])){
			echo '<div class="alert alert-danger">Tidak ada gambar yang dipilih.</div>';
	} else {
		if ($idlogo_elv) {
			update_option( 'logo_elv', $logo_elv);
		} else {
			$deprecated = null;
			$autoload = 'no';
			add_option( 'logo_elv', $logo_elv, $deprecated, $autoload );
		}
		echo '<div class="alert alert-success">Gambar berhasil diupdate.</div>';
	}
}

?>

<div class="card py-4 px-3 mb-3">
	<h6 class="elv-judulform">Logo Universitas</h6>
	
	<?php if (isset($_POST['logo_elv']) && !empty($_POST['logo_elv'])) {
	echo wp_get_attachment_image( $_POST['logo_elv'], 'medium' );
	} elseif ($idlogo_elv) {
	echo wp_get_attachment_image( $idlogo_elv, 'medium' );
	} ?>
	
	<div><a style="cursor:pointer" class="btn btn-secondary btn-sm text-white mb-2" onClick="open_media_uploader_image();">Pilih Gambar</a></div>
	<div id="logo"></div>
	<form method="post">
		<input required type="hidden" name="logo_elv" id="idlogo" value="" readonly />
		<div><input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" class="btn btn-info" value="Simpan" /></div>
	</form>
</div>

<div class="card py-4 px-3">
<table class="table border-first-0">
	<?php foreach ($array_option as $option) {
		echo '<tr><td>'.$option['title'].'</td>';
		echo '<th scope="row"><a id="'.$option['id'].'" class="text-info ubahopt">';
		$getoption = get_option($option['id']);
		if ($getoption=='ya') {
			echo '<i class="fa fa-toggle-on fa-2x"></i>';
		} else {
			echo '<i class="fa fa-toggle-off fa-2x"></i>';
		}
		echo '</a></th></tr>';
	} ?>
</table>
</div>


<script>
var media_uploader = null;
function open_media_uploader_image() {
    media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });
    media_uploader.on("insert", function(){
        var json = media_uploader.state().get("selection").first().toJSON();
        var image_url = json.url;
        //var image_caption = json.caption;
        //var image_title = json.title;
        var image_id = json.id;
		document.getElementById('logo').innerHTML = image_url;
		document.getElementById('idlogo').value = image_id;
    });
    media_uploader.open();
}
	jQuery(document).ready(function($){
		$(document).on("click",".ubahopt",function(e){
			var get_id = $(this).attr("id");
			$.ajax({  
				type: "POST",
				data: "action=pengaturan&id=" + get_id,
				url: sia_ajaxurl,
				success:function(data) {
					$("#" + get_id).html(data);
					$("#" + get_id).toggleClass("On");
					$("#" + get_id).toggleClass("Off");
				}
			});
		});
	});
</script>


