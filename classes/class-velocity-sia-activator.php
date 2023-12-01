<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    velocity-sia
 * @subpackage velocity-sia/classes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Velocity_SIA_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        self::create_velocity_sia();
        flush_rewrite_rules();
	}

    public static function create_velocity_sia() {
        
        global $wpdb;
        date_default_timezone_set('Asia/Jakarta'); 
        $table_name = $wpdb->prefix . "v_mata_kuliah";
        $v_fakultas = $wpdb->prefix . "v_fakultas";
        $v_prodi = $wpdb->prefix . "v_prodi";
        $v_jadwal = $wpdb->prefix . "v_jadwal";
        $v_krs = $wpdb->prefix . "v_krs";
        $v_khs = $wpdb->prefix . "v_khs";
        $v_kelas = $wpdb->prefix . "v_kelas";
        $v_ruang = $wpdb->prefix . "v_ruang";
        $v_materi = $wpdb->prefix . "v_materi";
        $v_tugas = $wpdb->prefix . "v_tugas";
        $v_quiz = $wpdb->prefix . "v_quiz";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id_makul INT(3) ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
                nama_makul text NOT NULL,
                tahun_akademik text NOT NULL,
                semester text NOT NULL,
                jenis_makul text NOT NULL,
                sks text NOT NULL,
                id_dosen text NOT NULL,
                id_prodi text NOT NULL,
                PRIMARY KEY (id_makul)
        );";
        dbDelta($sql);

        $sqlv_fakultas = "CREATE TABLE IF NOT EXISTS $v_fakultas (
                id_fakultas INT(3) ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
                nama_fakultas text NOT NULL,
                PRIMARY KEY (id_fakultas)
        );";
        dbDelta($sqlv_fakultas);

        $sqlv_prodi = "CREATE TABLE IF NOT EXISTS $v_prodi (
                id_prodi INT(3) ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
                nama_prodi text NOT NULL,
                id_fakultas text NOT NULL,
                PRIMARY KEY (id_prodi)
        );";
        dbDelta($sqlv_prodi);

        $sqlv_jadwal = "CREATE TABLE IF NOT EXISTS $v_jadwal (
                id_jadwal INT UNSIGNED NOT NULL AUTO_INCREMENT,
                hari text NOT NULL,
                jam_kuliah_awal text NOT NULL,
                jam_kuliah_akhir text NOT NULL,
                kelas text NOT NULL,
                ruang text NOT NULL,
                kuota text NOT NULL,
                id_makul text NOT NULL,
                PRIMARY KEY (id_jadwal)
        );";
        dbDelta($sqlv_jadwal);

        $sqlv_krs = "CREATE TABLE IF NOT EXISTS $v_krs (
                id_krs INT UNSIGNED NOT NULL AUTO_INCREMENT,
                id_mahasiswa text NOT NULL,
                id_jadwal text NOT NULL,
                tahun_akademik text NOT NULL,
                semester text NOT NULL,
                PRIMARY KEY (id_krs)
        );";
        dbDelta($sqlv_krs);

        $sqlv_khs = "CREATE TABLE IF NOT EXISTS $v_khs (
                id_khs INT UNSIGNED NOT NULL AUTO_INCREMENT,
                id_makul text NOT NULL,
                id_mahasiswa text NOT NULL,
                nilai text NOT NULL,
                PRIMARY KEY (id_khs)
        );";
        dbDelta($sqlv_khs);

        $sqlv_kelas = "CREATE TABLE IF NOT EXISTS $v_kelas (
                id INT(3) ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
                nama text NOT NULL,
                PRIMARY KEY (id)
        );";
        dbDelta($sqlv_kelas);

        $sqlv_ruang = "CREATE TABLE IF NOT EXISTS $v_ruang (
                id INT(3) ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
                nama text NOT NULL,
                PRIMARY KEY (id)
        );";
        dbDelta($sqlv_ruang);

        $sqlv_materi = "CREATE TABLE IF NOT EXISTS $v_materi (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                tanggal text NOT NULL,
                iduser text NOT NULL,
                tujuan text NOT NULL,
                detail text NOT NULL,
                PRIMARY KEY (id)
        );";
        dbDelta($sqlv_materi);

        $sqlv_tugas = "CREATE TABLE IF NOT EXISTS $v_tugas (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                tipe text NOT NULL,
                tanggal text NOT NULL,
                iduser text NOT NULL,
                tujuan text NOT NULL,
                detail text NOT NULL,
                PRIMARY KEY (id)
        );";
        dbDelta($sqlv_tugas);

        $sqlv_quiz = "CREATE TABLE IF NOT EXISTS $v_quiz (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                tipe text NOT NULL,
                tanggal text NOT NULL,
                iduser text NOT NULL,
                tujuan text NOT NULL,
                detail text NOT NULL,
                PRIMARY KEY (id)
        );";
        dbDelta($sqlv_quiz);

        // add role
        add_role('dosen', __('Dosen'),
            array(
                'read'              => true, // Allows a user to read
                'create_posts'      => false, // Allows user to create new posts
                'edit_posts'        => false, // Allows user to edit their own posts
                'edit_others_posts' => false, // Allows user to edit others posts too
                'publish_posts'     => false, // Allows the user to publish posts
                'manage_categories' => true, // Allows user to manage post categories
                'upload_files' => true, // Allows user to upload files
            )
        );
        add_role('mahasiswa', __('Mahasiswa'),
            array(
                'read'              => false, // Allows a user to read
                'create_posts'      => false, // Allows user to create new posts
                'edit_posts'        => false, // Allows user to edit their own posts
                'edit_others_posts' => false, // Allows user to edit others posts too
                'publish_posts'     => false, // Allows the user to publish posts
                'manage_categories' => false, // Allows user to manage post categories
                'upload_files' => true,
            )
        );

    }

}