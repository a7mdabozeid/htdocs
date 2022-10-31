<?php
namespace Razzi\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader.
 *
 */
class Auto_Loader {
	private static $files;

	/**
	 * Register files
	 *
	 * @since 1.0.0
	 *
	 * @return void/boolen
	 */
	public static function register( $pathes ) {
		foreach ( $pathes as $namespace => $filename ) {
			self::$files[ $namespace ] = $filename;
		}
		return true;
	}


	/**
	 * Load files
	 *
	 * @since 1.0.0
	 *
	 * @return void/boolen
	 */
	public static function load( $class ) {
		if ( isset( self::$files[ $class ] ) ) {
			require self::$files[ $class ];
		}
		return true;
	}

}
