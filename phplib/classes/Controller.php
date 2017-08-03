<?php
	namespace Application ;

	class Controller extends Base {
		public function __construct( $creator = null ) {
			parent::__construct( $creator ) ;

			$this->position = new Model\Position( $this ) ;
			$this->employer = new Model\Employer( $this ) ;
		}

		public function arg( $name ) {
			$request = &$this->creator->request ;

			if ( ! isset( $request[ $name ] ) ) {
				return null ;
			}

			return $request[ $name ] ;
		}

		public function indexAction( ) {
			return array(
				'view' => 'index' ,
				'result' => array( )
			) ;
		}

		public function employerAction( ) {
			$this->employer->action( ) ;

			$employee = $this->employer->all( true ) ;

			$result = array(
				'result' => array(
					'data' =>  array(
						'rows' => $employee ,
						'position' => $this->position->all( false )
					)
				) ,
				'view' => 'results-table'
			) ;

			return $result ;
		}

		public function positionAction( ) {
			$this->position->action( ) ;

			$positions = $this->position->all( true ) ;

			$result = array(
				'result' => array(
					'data' =>  array(
						'rows' => $positions
					)
				) ,
				'view' => 'results-table'
			) ;

			return $result ;
		}

		public function messageAction( ) {
			return array(
				'result' => array(
					'headers' => array(
						array(
							$this->creator->config[ 'http' ][ 'header' ][ 'default_name' ] ,
							$this->creator->config[ 'http' ][ 'header' ][ 'javascript' ]
						)
					) ,
					'key' => 'message' ,
					'data' => $this->creator->config[ 'message' ]
				) ,
				'view' => 'json' ,
				'passthru' => true
			) ;
		}
	}
