<?php
	namespace Application ;

	use \Smarty ;

	class View extends Base {
		const DEFAULT_STATE = 200 ;
		protected $parser ;

		protected function prepare( ) {
			if ( parent::prepare( ) ) {
				return true ;
			}

			$this->parser = new \Smarty( ) ;
			$this->parser->setTemplateDir( $this->getCurrentPath( $this->creator->config[ 'view' ][ 'path' ] ) ) ;
			$this->parser->setCompileDir( $this->getCurrentPath( $this->creator->config[ 'view' ][ 'compiled' ] ) ) ;

			return false ;
		}

		public function execute( $args ) {
			parent::execute( $args ) ;

			if ( empty( $args[ 'result' ][ 'headers' ] ) ) {
				$args[ 'result' ][ 'headers' ] = array( ) ;
			} else {
				if ( empty( $args[ 'result' ][ 'state' ] ) ) {
					$args[ 'result' ][ 'state' ] = self::DEFAULT_STATE ;
				}

				array_unshift(
					$args[ 'result' ][ 'headers' ] ,
					$this->getHTTPHeader( $args[ 'result' ][ 'state' ] )
				) ;
			}

			if ( empty( $args[ 'result' ][ 'meta' ] ) ) {
				$args[ 'result' ][ 'meta' ] = ( array ) $this->creator->config[ 'view' ][ 'meta' ] ;
			}

			$this->sendHeaders( $args[ 'result' ][ 'headers' ] ) ;
			$this->sendBody( $args ) ;

			return false ;
		}

		protected function getHTTPHeader( $state = self::DEFAULT_STATE , $http_message = null ) {
			if ( isset( $this->creator->config[ 'http' ][ 'state' ][ $state ][ 'message' ] ) ) {
				$http_message = $this->creator->config[ 'http' ][ 'state' ][ $state ][ 'message' ] ;
			}

			$result = sprintf( $this->creator->config[ 'http' ][ 'header' ][ 'pattern' ] ,
				$this->creator->config[ 'http' ][ 'version' ] , $state , $http_message
			) ;

			return $result ;
		}

		public function error( $state = self::DEFAULT_STATE , $message = null ) {
			$headers = array( ) ;
			$this->execute( array(
				'router' => $router ,
				'result' => array(
					'state' => $state ,
					'headers' => &$headers ,
					'data' => array(
						'message' => &$message
					)
				) ,
				'view' => $this->creator->config[ 'view' ][ 'error' ]
			) ) ;
		}

		protected function sendBody( &$args ) {
			$include_file = $this->getViewPath( $args[ 'view' ] , true ) ;
			$default_view = $this->getViewPath( null , true ) ;

			if ( empty( $include_file ) ) {
				$include_file = $this->getViewPath( $this->creator->config[ 'view' ][ 'index' ] ,
					true ) ;
			}

			$this->parser->assign( 'result' , $args[ 'result' ] ) ;
			$this->parser->assign( 'config' , $this->creator->config ) ;

			if ( empty( $args[ 'passthru' ] ) ) {
				$this->parser->assign( 'include' , $include_file ) ;
				$this->parser->display( $default_view ) ;
			} else {
				$this->parser->display( $include_file ) ;
			}

			return $this ;
		}

		protected function sendHeaders( &$headers ) {
                        if ( headers_sent( ) ) {
				return false ;
			}

			$header_default_name_found = false ;
			$http_header_sent = false ;

			foreach ( $headers as $i => $header ) {
				if ( is_string( $header ) ) {
					if ( $http_header_sent ) {
						continue ;
					}

					header( $header ) ;
					$http_header_sent = true ;

					continue ;
				}

				header( sprintf( $this->creator->config[ 'http' ][ 'header' ][ 'sub_pattern' ] , $header[ 0 ] , $header[ 1 ] ) ) ;

				if ( $header_default_name_found ) {
					continue ;
				}

				if ( strToLower( $header[ 0 ] ) !=
					strToLower( $this->creator->config[ 'http' ][ 'header' ][ 'default_name' ] ) ) {
					continue ;
				}

				$header_default_name_found = true ;
			}

			if ( $header_default_name_found ) {
				return true ;
			}

			header( $this->creator->config[ 'http' ][ 'header' ][ 'default' ] ) ;

			return true ;
		}

		protected function getViewPath( $name = null , $real_path = false ) {
			if ( is_null( $name ) ) {
				$name = $this->creator->config[ 'view' ][ 'default' ] ;
			}

			$viewPath = sprintf( $this->creator->config[ 'view' ][ 'pattern' ] ,
				$this->getCurrentPath( $this->creator->config[ 'view' ][ 'path' ] ) , $name ) ;

			if ( $real_path ) {
				$viewPath = realpath( $viewPath ) ;
			}

			return $viewPath ;
		}
	}
