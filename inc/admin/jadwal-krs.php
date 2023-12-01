<?php 
$opt_waktuawal = get_option( 'waktuawal');
$opt_waktuakhir = get_option( 'waktuakhir');
$waktuawal = isset($_POST['waktuawal']) ? $_POST['waktuawal'] : '';
$waktuakhir = isset($_POST['waktuakhir']) ? $_POST['waktuakhir'] : '';

if(isset($_POST['waktuawal']) || isset($_POST['waktuakhir']) || isset($_POST['submit'])){
if (!(wp_get_current_user()->user_login == "admindemo")){
	if (get_option('waktu_krs')=='ada') {
	    update_option( 'waktuawal', $waktuawal);
	    update_option( 'waktuakhir', $waktuakhir);
	} else {
	    $deprecated = null;
	    $autoload = 'no';
	    add_option( 'waktuawal', $waktuawal, $deprecated, $autoload );
	    add_option( 'waktuakhir', $waktuakhir, $deprecated, $autoload );
	    add_option( 'waktu_krs', 'ada', $deprecated, $autoload );
	}
$opt_waktuawal = $waktuawal;
$opt_waktuakhir = $waktuakhir;
echo '<div class="alert alert-success">Jadwal KRS berhasil diupdate.</div>';
}
} ?>
<form class="border border-info card p-4 mb-3" method='POST'>
	<div class="form-group mb-3">
			<label for="waktuawal" class="mb-1">Tanggal Mulai KRS</label>	
			<input required class="form-control" id="awal" type="text" name="waktuawal" value="<?php echo $opt_waktuawal; ?>" placeholder="dd-mm-yyyy" />
	</div>
	<div class="form-group mb-3">
		<label for="waktuakhir" class="mb-1">Tanggal Selesai KRS</label>
		<input required class="form-control" id="akhir" type="text" name="waktuakhir" value="<?php echo $opt_waktuakhir; ?>" placeholder="dd-mm-yyyy" />
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-info" value="Simpan"/>
	</div>
</form>


<div class="border border-danger rounded p-4 my-4">
	<div class="alert alert-danger">Penting!! Silahkan setting zona waktu untuk website anda terlebih dahulu.</div>
	<?php
	$timezone = get_option( 'timezone_string' );
	$gmt = get_option( 'gmt_offset' );
	echo "Zona waktu anda adalah ";
	if ($timezone) {
		echo "<strong>".$timezone."</strong>";
	} elseif ($gmt < 0) {
		echo "<strong>UTC".$gmt."</strong>";		
	} else {
		echo "<strong>UTC+".$gmt."</strong>";
	} ?>
	<br/>Untuk mengubah zona waktu <a href="<?php echo home_url();?>/wp-admin/options-general.php#timezone_string" target="_blank">klik disini</a>, dan ubah pada bagian <strong>Timezone</strong>.<br/>
	Untuk WIB ubah menjadi <strong>Asia -> Jakarta</strong> atau <strong>UTC+7</strong>.<br/>
	Untuk WITA ubah menjadi <strong>Asia -> Makassar</strong> atau <strong>UTC+8</strong>.<br/>
	Untuk WIT ubah menjadi <strong>Asia -> Jayapura</strong> atau <strong>UTC+9</strong>.
</div>


<script>
jQuery(document).ready(function ($) {
$('#awal, #akhir').datetimepicker({
	onGenerate:function( ct ){
		$(this).find('.xdsoft_date')
			.toggleClass('xdsoft_disabled');
	},
	format:'d-m-Y',
	formatDate:'d-m-Y',
	minDate:'-1970/0/0',
	maxDate:'+1970/0/0',
	timepicker:false
});
});
</script>
