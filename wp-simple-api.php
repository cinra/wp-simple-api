<?php
/**
 * Plugin Name: WP Simple API
 * Description: WP Simple API Plugin provides simple apis for wordpress.
 * Version: 0.0.1
 * Text Domain: wp-simple-api
 */

if (!defined('WP_SIMPLE_API_INCLUDE_PATH')) define('WP_SIMPLE_API_INCLUDE_PATH', WP_CONTENT_DIR . '/includes');

function wp_simple_api()
{
  global $wp;
  if ( preg_match('(^api)', $wp->request) )
  {
    $classname = strtr(ucwords(strtr(str_replace('/', '_', preg_replace( '/\.(.*)/', '', $wp->request)), ['_' => ' '])), [' ' => '']);
    $classpath = WP_SIMPLE_API_INCLUDE_PATH . '/' . preg_replace( '/\.(.*)/', '', $wp->request) . '.php';

    if (!is_readable($classpath)) return;

    include_once( $classpath );
    $api = new $classname( $wp->request );
  }
}
add_action( 'template_redirect', 'wp_simple_api');

class WpSimpleApi
{

  protected $data, $format, $method, $status;

  function __construct( $path )
  {
    $paths = pathinfo( $path );

    $this->success();
    $this->format = isset($paths['extension']) ? $paths['extension'] : 'html';
    $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';

    switch($this->method)
    {
      case 'post':
      $this->post();
      break;

      case 'put':
      $this->put();
      break;

      case 'delete':
      $this->delete();
      break;

      default:
      case 'get':
      $this->get();
      break;
    }

    return $this->response( $this->data );
  }

  function get()
  {
  }

  function post()
  {
  }

  function put()
  {
  }

  function delete()
  {
  }

  function success()
  {
    $this->status = 'success';
  }

  function error()
  {
    $this->status = 'error';
  }

  function response()
  {
    status_header( $this->status !== 'success' ? 400 : 200 );

    switch($this->format)
    {
      case 'xml':
      header("Content-Type: text/xml");
      echo $this->data;
      break;

      case 'html':
      header("Content-Type: text/html");
      echo $this->data;
      break;

      default:
      case 'json':
      header('Content-Type: application/json');
      echo json_encode($this->data);
      break;
    }

    exit;
  }

}