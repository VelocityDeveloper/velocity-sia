<?php

/**
 * function register asset css and sj to frontend public.
 *
 * @package Velocity SIA
 */

// Add custome scripts and styles
function v_el_scripts() {
	$wptheme = wp_get_theme( 'velocity' );
	if (!$wptheme->exists()) {
		wp_enqueue_style( 'elv-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style( 'elv-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
		wp_enqueue_script( 'elv-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array(), null, true );
	}
	wp_enqueue_style( 'el_style', VELOCITY_SIA_PLUGIN_DIR_URI . '/css/el-style.css');
	wp_enqueue_script( 'elearningjs', VELOCITY_SIA_PLUGIN_DIR_URI . '/js.js', array(), null, true );
	wp_enqueue_script( 'elv-print', VELOCITY_SIA_PLUGIN_DIR_URI . '/js/print.js', array(), null, true );
	wp_enqueue_script( 'elv-datetimepicker', VELOCITY_SIA_PLUGIN_DIR_URI . '/js/jquery.datetimepicker.full.min.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'v_el_scripts' );