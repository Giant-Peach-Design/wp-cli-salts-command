<?php

use SecurityLib\Strength;
use RandomLib\Factory;

/**
 * Manage salts.
 *
 * @author Sebastiaan de Geus
 */
class Salts_Command extends WP_CLI_Command {

  const ALL_CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_ []{}<>~`+=,.;:/?|!@#$%^&*()';

  /**
   * Salts that need to be generated
   *
   * @var array
   */
  protected static $salts_info = [];

  function __construct() {
    self::$salts_info = [
      'AUTH_KEY'          => 64,
      'SECURE_AUTH_KEY'   => 64,
      'LOGGED_IN_KEY'     => 64,
      'NONCE_KEY'         => 64,
      'AUTH_SALT'         => 64,
      'SECURE_AUTH_SALT'  => 64,
      'LOGGED_IN_SALT'    => 64,
      'NONCE_SALT'        => 64,
      'WP_CACHE_KEY_SALT' => 32,
    ];
  }

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
   * @when before_wp_load
   * @synopsis [--file=<foo>] [--format=<php,yaml,env>]
   *
   */
  function generate( $args, $assoc_args ) {
    $defaults = array(
      'format' => 'php',
    );
    $assoc_args = array_merge( $defaults, $assoc_args );

    $salts_data = self::_generate_salts();
    $output = self::_format_data( $salts_data, $assoc_args['format'] );

    if ( isset( $assoc_args['file'] ) ) {
      
      $file = (string)$assoc_args['file'];

      if ( file_exists( $file ) && ! is_writable( $file ) )
        WP_CLI::error( 'File is not writable or path is not correct: ' . $file );

      $file_flags = ( file_exists( $file ) ) ? FILE_APPEND : 0;
      if ( ! file_put_contents( $file, $output, $file_flags ) )
        WP_CLI::error( 'Could not write salts to: ' . $file );

      WP_CLI::success( 'Added salts to: ' . $file );
      return;
    }

    fwrite( STDOUT, $output );
  }

  private static function _format_data( $data, $format ) {
    $template = false;
    $line_end = PHP_EOL;
    $call_func = 'strtoupper';

    switch ( $format ) {
      case 'env':
        $template = "%s='%s'";
        $line_end = "\n";
        break;

      case 'yaml':
        $call_func = 'strtolower';
        $template = '%s: "%s"';
        break;

      case 'php':
      default:
        $template = "define( '%s', '%s' );";
        break;
    }

    $formatted = array_map(function ( $name, $salt ) use( $template, $call_func ) {
      $name = call_user_func( $call_func, $name );
      return sprintf( $template, $name, $salt );
    }, array_keys( $data ), $data );
    $formatted = implode( $line_end, $formatted );
    $formatted = $line_end . $formatted . $line_end;

    return $formatted;
  }

  private static function _generate_salts() {
    $factory = new Factory();
    $generator = $factory->getGenerator( new Strength(Strength::MEDIUM ) );
    $salts = [];
    array_map( function ( $key, $length ) use ( &$salts, $generator ) {
      $salts[ $key ] = $generator->generateString( $length, self::ALL_CHARACTERS );
    }, array_keys( self::$salts_info ), self::$salts_info );
    return $salts;
  }

}

WP_CLI::add_command( 'salts', 'Salts_Command' );
