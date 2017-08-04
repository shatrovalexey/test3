<?php
	/** базовый класс
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/**
	* @subpackage Application\Base базовый класс
	*/
	abstract class Base {
		/**
		* @var stdclass $creator объект-создатель
		*/
		public $creator ;

		/**
		* @var boolean $prepared признак подготовленности объекта к выполнению
		*/
		protected $prepared ;

		/**
		* Создание объекта
		* @param stdclass $creator ссылка на объект-создатель
		* @param mixed $args дополнительные аргументы
		*/
		public function __construct( $creator = null , $args = null ) {
			/** если создатель не указан, то объект - создатель сам себя
			* @var stdclass $creator объект-создатель
			*/
			if ( empty( $creator ) ) {
				$this->creator = $this ;
			} else {
				$this->creator = $creator ;
			}

			/**
			* @var boolean $prepared объект готов к выполнению
			*/
			$this->prepared = false ;

			// подготовка к выполнению
			$this->prepare( ) ;
		}

		/**
		* Подгоовка объекта к выполнению, выполняется для объекта однажды
		* @return boolean
		*/
		protected function prepare( ) {
			if ( $this->prepared ) {
				return true ;
			}

			$this->prepared = true ;

			return false ;
		}

		/**
		* Выполнения объекта
		* @return boolean
		*/
		public function execute( ) {
			if ( $this->prepared ) {
				return true ;
			}

			$this->prepare( ) ;

			return false ;
		}

		/**
		* Завершение выполнения объекта
		* @return boolean
		*/
		public function finish( ) {
			return $this->prepared ;
		}

		/**
		* Получение текущей директории
		* @param string $path путь к файлу
		* @param boolean $real_path возвращать только реальные пути
		* @return string
		*/
		public function getCurrentPath( $path = null , $real_path = false ) {
			/**
			* @var string $path путь к файлу
			*/
			$path = __DIR__ . DIRECTORY_SEPARATOR . "$path" ;

			// определение пути в соотношении с файловой системой
			if ( $real_path ) {
				$path = real_path( $path ) ;
			}

			return $path ;
		}
	}
