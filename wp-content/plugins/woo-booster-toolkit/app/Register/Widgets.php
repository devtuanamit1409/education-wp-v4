<?php

namespace WCBT\Register;

use WCBT\Helpers\Config;

class Widgets {
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}

	public function register_sidebars() {
		$sidebars = Config::instance()->get( 'sidebars', 'widgets' );

		if ( ! empty( $sidebars ) ) {
			foreach ( $sidebars as $sidebar ) {
				register_sidebar( $sidebar );
			}
		}

		$files = glob( WCBT_DIR . 'app/Widgets/*.php' );
		foreach ( $files as $file ) {
			require_once $file;

			$file_names = explode( '/', $file );
			$file_name  = end( $file_names );
			$file_name  = str_replace( '.php', '', $file_name );
			$class_name = 'WCBT\Widgets\\' . $file_name;
			register_widget( $class_name );
		}
	}
}
