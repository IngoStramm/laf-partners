<?php
// SCRIPTS

$min = ( in_array( $_SERVER[ 'REMOTE_ADDR' ], array( '127.0.0.1', '::1' ) ) ) ? '' : '.min';

function lp_enqueue_scripts() {

	wp_enqueue_style( 'lp-style', LP_URL . '/css/style.css', array(), '1.0.0' );

	// Grunt main file with Bootstrap, FitVids and others libs.
	wp_enqueue_script( 'lp-main-min', LP_URL . '/js/main' . $min . '.js', array( 'jquery' ), null, true );


	// Grunt watch livereload in the browser.
	if( in_array( $_SERVER[ 'REMOTE_ADDR' ], array( '127.0.0.1', '::1' ) ) )
		wp_enqueue_script( 'lp-livereload', 'http://localhost:35729/livereload.js?snipver=1', array(), null, true );

}

add_action( 'wp_enqueue_scripts', 'lp_enqueue_scripts', 1 );