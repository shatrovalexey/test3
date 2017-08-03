<?php
	namespace Application ;
	use Noodlehaus\Config ;

	class Router extends Base {
		public $redirect_url ;
		protected $redirect_url_name ;
		public $headers ;
		public $data ;
		public $state ;

		protected function prepare( ) {
			$this->redirect_url = &$this->creator->server[ 'REDIRECT_URL' ] ;
			$this->redirect_url_name = substr( $this->redirect_url , 1 ) ;
			$this->headers = $this->body = array( ) ;

			return false ;
		}

		public function execute( $data = null ) {
			foreach ( $this->creator->config[ 'router' ][ 'map' ] as $route => $ctrl ) {
				if ( $route != $this->redirect_url_name ) {
					continue ;
				}

				$router = $this ;

				return function( ) use( $router , &$route , &$ctrl ) {
					$action = sprintf(
						$router->creator->config[ 'router' ][ 'action' ] ,
						$ctrl
					) ;

					$result = $router->creator->controller->$action( ) ;
					$result = $router->creator->view->execute( array(
						'router' => $router ,
						'result' => $result[ 'result' ] ,
						'view' => $result[ 'view' ] ,
						'passthru' => ! empty( $result[ 'passthru' ] )
					) ) ;

					return $result ;
				} ;
			}

			return null ;
		}

		public function redirect_back( ) {
			header( 'Location: ' . $this->creator->server[ 'HTTP_REFERER' ] ) ;
			exit( 0 ) ;
		}
	}
