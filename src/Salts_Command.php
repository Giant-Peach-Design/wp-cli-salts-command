<?php

use Salaros\WordPress\SaltsGenerator;

/**
* Manage salts via WP CLI.
*/
class Salts_Command extends WP_CLI_Command {

	/**
	* Generates salts to STDOUT or to a file.
	*
	* ## OPTIONS
	*
	* [--file=<file>]
	* : The name of the file to write to. Default outputs to STDOUT.
	*
	* [--format=<format>]
	* : Can be php, yaml or env. Defaults to php.
	*
	* [--append=<SALT1,SALT2>]
	* : A coma-separated list of additional salt variables to generate.
	*
	* @when before_wp_load
	* @synopsis [--file=<foo>] [--format=<php,yaml,env>] [--append=<SALT1,SALT2>]
	*
	*/
	function generate( $args, $assoc_args ) {
		$defaults = array(
			'format' => '',
			'append' => '',
		);
		$assoc_args = array_merge( $defaults, $assoc_args );
		$file_format = (string) $assoc_args['format'];
		$append_salts = explode( ',', (string) $assoc_args['append'] );

		if ( ! isset( $assoc_args['file'] ) || empty( $assoc_args['file'] ) ) {
			$salts_formatted = SaltsGenerator::generateFormattedSalts( $file_format, $append_salts );
			fwrite( STDOUT, $salts_formatted );
			return;
		}

		$out_file = (string) $assoc_args['file'];
		if ( file_exists( $out_file ) && ! is_writable( $out_file ) ) {
			WP_CLI::error( 'File is not writable or path is not correct: ' . $out_file );
		}

		if ( ! SaltsGenerator::writeToFile( $file_format, $out_file, $append_salts ) ) {
			WP_CLI::error( 'Could not write salts to: ' . $out_file );
		}

		WP_CLI::success( 'Added salts to: ' . $out_file );
	}
}

WP_CLI::add_command( 'salts', 'Salts_Command' );
