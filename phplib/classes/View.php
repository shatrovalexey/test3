<?php
	/** общий класс для моделей
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/**
	* @uses \Smarty шаблонизатор
	*/
	use \Smarty ;

	/**
	* @subpackage Application\View представление
	*/
	class View extends Base {
		/**
		* @const integer DEFAULT_STATE код HTTP-ответа по-умолчанию
		*/
		const DEFAULT_STATE = 200 ;

		/**
		* @var \Smarty $parser объект шаблонизатора
		*/
		protected $parser ;

		/**
		* подготовка к выполнению основной задачи объекта
		* @return void
		*/
		protected function prepare( ) {
			// выполнение метода родительского класса
			if ( parent::prepare( ) ) {
				return true ;
			}

			/**
			* @var \Smarty $parser маршрутизатор
			*/
			$this->parser = new \Smarty( ) ;

			// директория с шаблонами
			$this->parser->setTemplateDir( $this->getCurrentPath( $this->creator->config[ 'view' ][ 'path' ] ) ) ;

			// директория с кэшем
			$this->parser->setCompileDir( $this->getCurrentPath( $this->creator->config[ 'view' ][ 'compiled' ] ) ) ;

			return false ;
		}

		/**
		* выполнение основной задачи объекта
		* @param array $args аргументы
		* @return void
		*/
		public function execute( $args ) {
			// вызов метода родителя
			parent::execute( $args ) ;

			// если не назначен статус HTTP
			if ( empty( $args[ 'result' ][ 'state' ] ) ) {
				$args[ 'result' ][ 'state' ] = self::DEFAULT_STATE ;
			}

			// если не назначены заголовки HTTP
			if ( empty( $args[ 'result' ][ 'headers' ] ) ) {
				$args[ 'result' ][ 'headers' ] = array( ) ;
			}

			// добавить заголовок со статусом HTTP в заголовки
			array_unshift(
				$args[ 'result' ][ 'headers' ] ,
				$this->getHTTPHeader( $args[ 'result' ][ 'state' ] )
			) ;

			// значения по-умолчанию, если не назначены значения meta для страницы
			if ( empty( $args[ 'result' ][ 'meta' ] ) ) {
				$args[ 'result' ][ 'meta' ] = ( array ) $this->creator->config[ 'view' ][ 'meta' ] ;
			}

			// отправка заголовков HTTP
			$this->sendHeaders( $args[ 'result' ][ 'headers' ] ) ;

			// отправка тела HTTP
			$this->sendBody( $args ) ;
		}

		/**
		* получение строки загоовка со статусом HTTP
		* @param integer $state HTTP-статус
		* @param string $http_message сообщение в HTTP-заголовке со статусом
		* @return string
		*/
		protected function getHTTPHeader( $state = self::DEFAULT_STATE , $http_message = null ) {
			if ( isset( $this->creator->config[ 'http' ][ 'state' ][ $state ][ 'message' ] ) ) {
				$http_message = $this->creator->config[ 'http' ][ 'state' ][ $state ][ 'message' ] ;
			}

			return sprintf( $this->creator->config[ 'http' ][ 'header' ][ 'pattern' ] ,
				$this->creator->config[ 'http' ][ 'version' ] , $state , $http_message
			) ;
		}

		/**
		* вывод ошибки
		* @param integer $state HTTP-статус
		* @param string $message сообщение в теле HTTP-ответа
		* @return void
		*/
		public function error( $state = self::DEFAULT_STATE , $message = null ) {
			$this->execute( array(
				'router' => $router ,
				'result' => array(
					'state' => $state ,
					'headers' => array( ) ,
					'data' => array(
						'message' => &$message
					)
				) ,
				'view' => $this->creator->config[ 'view' ][ 'error' ]
			) ) ;
		}

		/**
		* вывод тела ответа HTTP
		* @param array $args дополнительные аргументы
		* @return void
		*/
		protected function sendBody( &$args ) {
			/**
			* @var string $include_file файл шаблона для включения
			*/
			$include_file = $this->getViewPath( $args[ 'view' ] , true ) ;

			// использовать файл по-умочанию, если файл шаблона для ключения не найден
			if ( empty( $include_file ) ) {
				$include_file = $this->getViewPath( $this->creator->config[ 'view' ][ 'index' ] ,
					true ) ;
			}

			$this->parser->assign( 'result' , $args[ 'result' ] ) ;
			$this->parser->assign( 'config' , $this->creator->config ) ;

			if ( empty( $args[ 'passthru' ] ) ) {
				/**
				* @var string $default_view файл шаблона по-умолчанию
				*/
				$default_view = $this->getViewPath( null , true ) ;

				$this->parser->assign( 'include' , $include_file ) ;
				$this->parser->display( $default_view ) ;
			} else {
				$this->parser->display( $include_file ) ;
			}
		}

		/**
		* вывод HTTP-заголовков
		* @param array $headers список HTTP-заголовков
		* @return boolean
		*/
		protected function sendHeaders( &$headers ) {
			// выход из метода, если HTTP-заголовки отправлены
                        if ( headers_sent( ) ) {
				return false ;
			}

			/**
			* @var boolean $header_default_name_found в переданных заголовках найден Content-Type
			*/
			$header_default_name_found = false ;

			/**
			* @var boolean $http_header_sent в переданных заголовках найден заголовок с HTTP-статусом
			*/
			$http_header_sent = false ;

			/** просмотр переданных заголовков
			* @var integer $i счётчик цикла
			* @var mixed $header данные заголовка
			*/
			foreach ( $headers as $i => $header ) {
				// текущий заголовок - заголовок HTTP-статуса
				if ( is_string( $header ) ) {
					// пропуск, если заголовок HTTP-статуса отправлен
					if ( $http_header_sent ) {
						continue ;
					}

					// вывод заголовка
					header( $header ) ;
					$http_header_sent = true ;

					continue ;
				}

				// вывод заголовка
				header( sprintf( $this->creator->config[ 'http' ][ 'header' ][ 'sub_pattern' ] , $header[ 0 ] , $header[ 1 ] ) ) ;

				// заголовок Content-Type выведен
				if ( $header_default_name_found ) {
					continue ;
				}

				// имя заголовка Content-Type
				if ( strToLower( $header[ 0 ] ) !=
					strToLower( $this->creator->config[ 'http' ][ 'header' ][ 'default_name' ] ) ) {
					continue ;
				}

				$header_default_name_found = true ;
			}

			// выход из метода, если заголовок Content-Type выведен
			if ( $header_default_name_found ) {
				return true ;
			}

			// вывод заголовка Content-Type по-умолчанию
			header( $this->creator->config[ 'http' ][ 'header' ][ 'default' ] ) ;

			return true ;
		}

		/** получение пути к файлу шаблона
		* @param string $name имя файла шаблона
		* @param boolean $real_path вернуть полный путь к файлу шаблона
		*/
		protected function getViewPath( $name = null , $real_path = false ) {
			/** если имя файла шаблона пустое, то использовать имя по-умолчанию
			*/
			if ( is_null( $name ) ) {
				$name = $this->creator->config[ 'view' ][ 'default' ] ;
			}

			/**
			* @var string $viewPath файл шаблона по имени
			*/			
			$viewPath = sprintf( $this->creator->config[ 'view' ][ 'pattern' ] ,
				$this->getCurrentPath( $this->creator->config[ 'view' ][ 'path' ] ) , $name ) ;

			/**
			* полный путь к файлу шаблона
			*/
			if ( $real_path ) {
				$viewPath = realpath( $viewPath ) ;
			}

			return $viewPath ;
		}
	}
