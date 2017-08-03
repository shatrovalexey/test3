<?php
	namespace Application ;

	abstract class Base {
		public $creator ;
		protected $prepared ;

		public function __construct( $creator = null , $args = null ) {
			if ( empty( $creator ) ) {
				$this->creator = $this ;
			} else {
				$this->creator = $creator ;
			}

			$this->prepared = false ;
			$this->prepare( ) ;
		}

		protected function prepare( ) {
			if ( $this->prepared ) {
				return true ;
			}

			$this->prepared = true ;

			return false ;
		}

		public function execute( $data = null ) {
			if ( $this->prepared ) {
				return true ;
			}

			$this->prepare( ) ;

			return false ;
		}

		public function finish( ) {
			return $this->prepared ;
		}

		public function getCurrentPath( $path = null , $real_path = false ) {
			$path = __DIR__ . DIRECTORY_SEPARATOR . "$path" ;

			if ( $real_path ) {
				$path = real_path( $path ) ;
			}

			return $path ;
		}
	}
