<?php
	namespace Application ;

        use Noodlehaus\Config ;

	class Application extends Base {
		protected $config_file_name ;
		public $config ;
		public $dbh ;
		public $router ;
		public $controller ;
		public $view ;

		public $request ;
		public $server ;

		public function __construct( $creator = null , $config_file_name = CONFIG_FILE_NAME ) {
			$this->config_file_name = $config_file_name ;
			$this->request = &$_REQUEST ;
			$this->server = &$_SERVER ;

			parent::__construct( $creator ) ;
		}

		protected function prepare( ) {
			if ( parent::prepare( ) ) {
				return true ;
			}

		        $this->config = new Config( CONFIG_FILE_NAME ) ;
        		$this->dbh = new \PDO(
                		$this->config[ 'db' ][ 'dsn' ] ,
		                $this->config[ 'db' ][ 'user' ] ,
		                $this->config[ 'db' ][ 'password' ]
		        ) ;
		        $this->dbh->query( 'SET names ' . $this->config[ 'db' ][ 'charset' ] ) ;

			$this->router = new Router( $this ) ;
			$this->controller = new Controller( $this ) ;
			$this->view = new View( $this ) ;

			return false ;
		}

		public function execute( $data = null ) {
			parent::execute( ) ;

			$route_h = $this->router->execute( ) ;
			$result = $route_h( ) ;

			return $result ;
		}

		public function finish( ) {
			if ( parent::finish( ) ) {
				$this->pdo->close( ) ;

				return true ;
			}

			return false ;
		}
	}
