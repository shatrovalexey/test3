<?php
	/** класс для согласованного выполнения программ пакета
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/** подключение класса чтения настроек
	* @uses Noodlehaus\Config
	*/
        use Noodlehaus\Config ;

	/**
	* @subpackage Application\Application класс, объединяющий прочие
	*/
	class Application extends Base {
		/**
		* @var string $config_file_name файл настроек
		*/
		protected $config_file_name ;

		/**
		* @var array $config найтройки в виде дерева
		*/
		public $config ;

		/**
		* @var PDO $dbh подключение к СУБД
		*/
		public $dbh ;

		/**
		* @var Application\Router $router объект машрутизатора
		*/
		public $router ;

		/**
		* @var Application\Controller $controller объект контроллера
		*/
		public $controller ;

		/**
		* @var Application\View $view объект представления
		*/
		public $view ;

		/**
		* @var array $request аргументы запроса
		*/
		public $request ;

		/**
		* @var array $request переменные окружения
		*/
		public $server ;

		/**
		* Создание объекта
		* @param stdclass $creator ссылка на объект-создатель
		* @param string $config_file_name путь к файлу настроек
		*/
		public function __construct( $creator = null , $config_file_name = CONFIG_FILE_NAME ) {
			/**
			* @var string $config_file_name путь к файлу настроек
			*/
			$this->config_file_name = $config_file_name ;

			/**
			* @var array $request аргументы запроса
			*/
			$this->request = &$_REQUEST ;

			/**
			* @var array $server переменные окружения
			*/
			$this->server = &$_SERVER ;

			// вызов инициализации объекта в методе Application\Base
			parent::__construct( $creator ) ;
		}

		/**
		* Подготовка, инициализация переменных объекта к выполнению
		* @return boolean
		*/
		protected function prepare( ) {
			// вызов подготовки, инициализации объекта в методе Application\Base
			if ( parent::prepare( ) ) {
				/**
				* если объект был уже подготовлен к выполнению,
				* то повторная подготовка не требуется
				*/

				return true ;
			}

			/**
			* @var Noodlehaus\Config $config загрузка дерева настроек
			*/
		        $this->config = new Config( CONFIG_FILE_NAME ) ;

			/**
			* @var PDO $dbh создание подключения к СУБД
			*/
        		$this->dbh = new \PDO(
                		$this->config[ 'db' ][ 'dsn' ] ,
		                $this->config[ 'db' ][ 'user' ] ,
		                $this->config[ 'db' ][ 'password' ]
		        ) ;

			// установка кодировки для подключения к СУБД
		        $this->dbh->query( 'SET names ' . $this->config[ 'db' ][ 'charset' ] ) ;

			/**
			* @var Application\Router $router объект машрутизатора
			*/
			$this->router = new Router( $this ) ;

			/**
			* @var Application\Controller $controller объект контроллера
			*/
			$this->controller = new Controller( $this ) ;

			/**
			* @var Application\View $view объект представления
			*/
			$this->view = new View( $this ) ;
 
			// возвращает false, если метод был запущены впервые
			return false ;
		}

		/**
		* Выполнение приложения
		* @return void
		*/
		public function execute( ) {
			// вызов родительского метода выполнения
			parent::execute( ) ;

			/**
			* @var closure $route_h связь контроллера и представления для выполнения
			*/
			$route_h = $this->router->execute( ) ;

			/**
			* @var mixed $result результат выполнение связи контроллера и представления
			*/
			$result = $route_h( ) ;
		}

		/**
		* Завершение выполнения объекта, выполняется однажды для объекта
		* @return boolean
		*/
		public function finish( ) {
			/**
			* Вызов метода родителя
			*/
			if ( parent::finish( ) ) {
				/**
				* Закрытие подключения к СУБД
				*/
				$this->pdo->close( ) ;

				return true ;
			}

			return false ;
		}
	}
