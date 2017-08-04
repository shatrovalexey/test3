<?php
	/** общий класс для моделей
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/**
	* @subpackage Application\Router маршрутизатор
	*/
	class Router extends Base {
		/**
		* @var string $redirect_url URL запроса
		*/
		public $redirect_url ;

		/**
		* @var string $redirect_url URL запроса без первого символа '/'
		*/
		protected $redirect_url_name ;

		/**
		* @var array $headers список HTTP-заголовков
		*/
		public $headers ;

		/**
		* @var array $data данные для вывода в теле HTTP-ответа
		*/
		public $data ;

		/**
		* @var integer $state код HTTP-ответа
		*/
		public $state ;

		/**
		* подготовка объекта к выполнению
		* @return boolean
		*/
		protected function prepare( ) {
			$this->redirect_url = &$this->creator->server[ 'REDIRECT_URL' ] ;
			$this->redirect_url_name = substr( $this->redirect_url , 1 ) ;
			$this->headers = array( ) ;

			return false ;
		}

		/**
		* выполнение главного метода объекта
		* @return array
		*/
		public function execute( ) {
			// возврат из метода, если маршрут не определён
			if ( empty( $this->creator->config[ 'router' ][ 'map' ][ $this->redirect_url_name ] ) ) {
				return array( ) ;
			}

			/** просмотр карты маршрутизации
			* @var string $route начальная точка машрута
			* @var string $ctrl имя метода в контроллере
			* @var Application\Router $router объект маршрутизатора
			*/
			$route = &$this->redirect_url_name ;
			$ctrl = $this->creator->config[ 'router' ][ 'map' ][ $this->redirect_url_name ] ;
			$router = $this ;

			return function( ) use( $router , &$route , &$ctrl ) {
				/**
				* @var string $action название метода в контролере
				*/
				$action = sprintf( $router->creator->config[ 'router' ][ 'action' ] , $ctrl ) ;

				/**
				* @var array $result результат выполнения метода контроллера
				*/
				$result = $router->creator->controller->$action( ) ;

				// обработка результата выполнения метода контроллера в представлении
				$router->creator->view->execute( array(
					'router' => $router ,
					'result' => $result[ 'result' ] ,
					'view' => $result[ 'view' ] ,
					'passthru' => ! empty( $result[ 'passthru' ] )
				) ) ;
			} ;
		}

		/**
		* возврат на предыдущую страницу
		* @return void
		*/
		public function redirect_back( ) {
			header( 'Location: ' . $this->creator->server[ 'HTTP_REFERER' ] ) ;
			exit( 0 ) ;
		}
	}
