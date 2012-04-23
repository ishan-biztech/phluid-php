<?php

require_once 'View.php';

class Phluid_Response {

  private $raw_body;
  private $status_code = 200;
  private $headers = array();
  private $request;
  private $app;
  
  function __construct( $app, $request ){
    $this->app = $app;
    $this->request = $request;
  }
  
  public function render( $template, $locals = array(), $options = array() ){
    $layout = Phluid_Utils::array_val( $options, 'layout', $this->app->default_layout );
    $status = Phluid_Utils::array_val( $options, 'status', 200 );
    $content_type = Phluid_Utils::array_val($options, 'content-type', 'text/html' );
    $locals['request'] = $this->request;
    $view = new Phluid_View( $template, $layout, $this->app->view_path );
    $this->renderString( $view->render( $locals ), $content_type, $status );
  }
  
  public function setHeader( $key, $value ){
    $this->headers[trim(strtoupper($key))] = $value;
  }
  
  public function getHeader( $key ){
    $key = strtoupper( $key );
    if ( array_key_exists( $this->headers, $key ) ) {
      return $this->headers[$key];
    }
  }
  
  public function eachHeader( $callback ){
    foreach( $this->headers as $name => $value ){
      $callback( $name, $value );
    }
  }
  
  public function statusHeader(){
    return "HTTP/1.0 " . $this->statusMessage();
  }
  
  public function statusMessage(){
    return (string) $this->status_code;
  }
  
  public function redirect( $url ){
    $this->setHeader( "Location", $url );
    $this->raw_body = "Redirecting: $url";
  }
  
  public function renderString( $string, $content_type="text/plain", $status = 200 ){
    $this->status_code = $status;
    $this->raw_body = (string) $string;
    $this->setHeader( 'Content-Type', $content_type );
  }
  
  public function renderText( $string, $content_type="text/plain", $status = 200 ){
    $this->renderString( $string, $content_type, $status );
  }
  
  public function renderJSON( $object, $status = 200 ){
    $this->renderString( json_encode($object), "application/json", $status );
  }
  
  public function getBody(){
    return $this->raw_body;
  }
  
  public function setBody( $body ){
    $this->raw_body = $body;
  }
  
  public function __toString(){
    return $this->statusHeader();
  }
  
  
}