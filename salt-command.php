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

    $api    = 'https://api.wordpress.org/secret-key/1.1/salt/';
    $data   = file_get_contents( $api );
    $output = self::_format_data( $data, $assoc_args['format'] );

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
    switch ( $format ) {
      case 'env':
        $pattern   = "/define\('([A-Z_]+)',\s*'(.+)'\);/";
        $formatted = "\n\n" . preg_replace($pattern, "$1='$2'", $data);
        $formatted .= sprintf( "WP_CACHE_KEY_SALT='%s'", self::_generate_salt(32) ) . "\n";
        break;

      case 'yaml':
        $data = str_replace("define('AUTH_KEY',", "auth_key:", $data);
        $data = str_replace("define('SECURE_AUTH_KEY',","secure_auth_key:", $data);
        $data = str_replace("define('LOGGED_IN_KEY',","logged_in_key:", $data);
        $data = str_replace("define('NONCE_KEY',","nonce_key:", $data);
        $data = str_replace("define('SECURE_AUTH_SALT',","secure_auth_salt:", $data);
        $data = str_replace("define('LOGGED_IN_SALT',","logged_in_salt:", $data);
        $data = str_replace("define('NONCE_SALT',","nonce_salt:", $data);
        $formatted = str_replace("');","\"", $data);
        $formatted = str_replace("'","\"", $formatted);
        $formatted .= sprintf( 'wp_cache_key_salt: "%s"', self::_generate_salt(32) ) . PHP_EOL;
        break;

      case 'php':
      default:
        $formatted = '<?php' . PHP_EOL . PHP_EOL . $data;
        $formatted .= sprintf( "define('WP_CACHE_KEY_SALT', '%s');", self::_generate_salt(32) ) . PHP_EOL;
        break;
    }

    return $formatted;
  }

  private static function _generate_salt( $length = 64 ) {
    $factory = new Factory();
    $generator = $factory->getGenerator( new Strength(Strength::MEDIUM ) );
    return $generator->generateString( $length, self::ALL_CHARACTERS );
  }
}

WP_CLI::add_command( 'salts', 'Salts_Command' );
