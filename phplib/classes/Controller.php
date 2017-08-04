<?php
	/** класс для согласованного выполнения программ пакета
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/**
	* @subpackage Application\Controller контроллер
	*/
	class Controller extends Base {
		/**
		* Создание объекта
		* @param stdclass $creator ссылка на объект-создатель
		*/
		public function __construct( $creator = null ) {
			// вызов метода родителя
			parent::__construct( $creator ) ;

			/**
			* @var Application\Model\Position $position объект модели должности
			*/
			$this->position = new Model\Position( $this ) ;

			/**
			* @var Application\Model\Employer $employer объект модели работника
			*/
			$this->employer = new Model\Employer( $this ) ;
		}

		/**
		* Получение значения HTTP-аргумента
		* @param string $name имя переменной HTTP-запроса
		* @return mixed
		*/
		public function arg( $name ) {
			$request = &$this->creator->request ;

			if ( ! isset( $request[ $name ] ) ) {
				return null ;
			}

			return $request[ $name ] ;
		}

		/**
		* Главная страница
		* @return array инструкции для дальнейшей обработки запроса
		* view - имя файла представления
		* result - дополнительные данные для обработки запроса
		*/
		public function indexAction( ) {
			return array(
				'view' => 'index' ,
				'result' => array( )
			) ;
		}

		/**
		* Список работников
		* @return array инструкции для дальнейшей обработки запроса
		* view - имя файла представления
		* result - дополнительные данные для обработки запроса
		* result.data - данные для вывода в теле HTTP-ответа
		* result.data.rows - массив даных для вывода в таблице
		* result.data.position - дополнительный массив даных для вывода внешних ключей в таблице
		*/
		public function employerAction( ) {
			// обработка HTTP-аргумента "action"
			$this->employer->action( ) ;

			return array(
				'result' => array(
					'data' =>  array(
						'rows' => $this->employer->all( true ) ,
						'position' => $this->position->all( false )
					)
				) ,
				'view' => 'results-table'
			) ;
		}

		/**
		* Список должностей
		* @return array инструкции для дальнейшей обработки запроса
		* view - имя файла представления
		* result - дополнительные данные для обработки запроса
		* result.data - данные для вывода в теле HTTP-ответа
		* result.data.rows - массив даных для вывода в таблице
		*/
		public function positionAction( ) {
			// обработка HTTP-аргумента "action"
			$this->position->action( ) ;

			return array(
				'result' => array(
					'data' =>  array(
						'rows' => $this->position->all( true )
					)
				) ,
				'view' => 'results-table'
			) ;
		}

		/**
		* Список сообщений для использования в JavaScript
		* @return array инструкции для дальнейшей обработки запроса
		* view - имя файла представления
		* passthru - выводить представление не внутри общего представления, а сруза
		* result.key - имя переменной для присвоения массива сообщений
		* result - дополнительные данные для обработки запроса
		* result.data - данные для вывода в теле HTTP-ответа
		* result.data.rows - массив даных для вывода в таблице
		*/
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
