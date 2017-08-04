<?php
	/** общий класс для моделей
	* @package Application описанные классы задачи
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	namespace Application ;

	/**
	* @subpackage Application\Model модель
	*/
	abstract class Model {
		/**
		* @var Application\Application $app объект основной программы
		*/
		protected $app ;

		/**
		* @var stdclass $creator объект-создатель
		*/
		protected $creator ;

		/**
		* @var PDO $dbh подключение к СУБД
		*/
		protected $dbh ;

		/**
		* @var string $_entityName имя сущности в БД
		*/
		protected $_entityName ;

		/**
		* @var string $_className имя текущего класса
		*/
		protected $_className ;

		/**
		* Создание объекта
		* @param stdclass $creator ссылка на объект-создатель
		*/
		public function __construct( $creator = null ) {
			$this->creator = $creator ;
			$this->app = $this->creator->creator ;
			$this->dbh = $this->app->dbh ;
		}

		/**
		* Получение значения HTTP-аргумента
		* @param string $name имя переменной HTTP-запроса
		* @return mixed
		*/
		protected function arg( $name ) {
			return $this->creator->arg( $name ) ;
		}

		/**
		* Получение имени текущего класса
		* @return string
		*/
		protected function className( ) {
			if ( ! empty( $this->_className ) ) {
				return $this->_className ;
			}

			$this->_className = ( new \ReflectionClass( $this ) )->getShortName( ) ;

			return $this->_className ;
		}

		/**
		* Получение имени сущности в БД, которую представляет текущий класс
		* @return string
		*/
		protected function entityName( ) {
			if ( ! empty( $this->_entityName ) ) {
				return $this->_entityName ;
			}

			$className = $this->className( ) ;
			$this->_entityName = strToLower( $className ) ;

			return $this->_entityName ;
		}

		/**
		* Получение списка столбцов сущности текущего класса
		* @return array
		*/
		protected function columns( ) {
			$entityName = $this->entityName( ) ;
			return $this->dbh->query( "
SELECT
	`t1`.`COLUMN_NAME` AS `name` ,
	`t1`.`COLUMN_COMMENT` AS `comment` ,
	CASE
		WHEN ( `t1`.`CHARACTER_SET_NAME` IS null ) THEN
			'number'
		WHEN ( `t1`.`DATA_TYPE` IN ( 'text' , 'tinytext' , 'longtext' ) ) THEN
			'text'
		ELSE
			'value'
	END AS `type` ,
	CASE `t1`.`COLUMN_KEY`
		WHEN 'PRI' THEN 'pri'
		WHEN 'MUL' THEN 'foreign'
		ELSE null
	END AS `key` ,
	CASE
		WHEN ( `t1`.`COLUMN_NAME` LIKE '%_id' ) THEN
			substring( `t1`.`COLUMN_NAME` FROM 1 FOR char_length( `t1`.`COLUMN_NAME` ) - 3 )
		ELSE
			null
	END AS `reference`
FROM
	`INFORMATION_SCHEMA`.`COLUMNS` AS `t1`
WHERE
	( `t1`.`TABLE_NAME` = '{$entityName}' ) AND
	( `t1`.`TABLE_SCHEMA` = database( ) )
ORDER BY
	`t1`.`ORDINAL_POSITION` ASC
			" )->fetchAll( \PDO::FETCH_ASSOC ) ;
		}

		/**
		* Обработка HTTP-аргумента "action"
		* @return void
		*/
		public function action( ) {
			switch ( $action = $this->arg( 'action' ) ) {
				// создание записи
				case 'create' :

				// обновление записи
				case 'update' :

				// удаление записи
				case 'delete' : {
					// выполнение соответствущего метода
					$this->$action( ) ;

					// возвращение на предыдущую страницу
					$this->creator->creator->router->redirect_back( ) ;
				}
			}
		}

		/**
		* Удаление записи
		* @return boolean
		*/
		public function delete( ) {
			/**
			* @var string $entityName имя сущности
			*/
			$entityName = $this->entityName( ) ;

			/**
			* @var array $primary_where условия удаления записи по первичному ключу
			*/
			$primary_where = array( ) ;

			/**
			* @var array $values значения для подстановки
			*/
			$values = array( ) ;

			/** просмотр столбцов
			* @var int $i счётчик цикла
			* @var array $column данные столбца
			*/
			foreach ( $this->columns( ) as $i => $column ) {
				// пропуск столбца, если столбец не входит в первичный ключ
				switch ( $column[ 'key' ] ) {
					case 'pri' : {
						break ;
					}
					default : {
						continue 2 ;
					}
				}

				/** пропуск столбца, если значение HTTP-аргумента - пустая строка
				* @var mixed $value значение HTTP-аргумента
				*/
				switch ( $value = $this->arg( $column[ 'name' ] ) ) {
					case '' : {
						continue 2 ;
					}
				}

				/**
				* @var string $key ключ для подстановок значений в запросе
				*/
				$key = ':' . $column[ 'name' ] ;

				// добавление выражения для отбора записей
				$primary_where[] = '`t1`.`' . $column[ 'name' ] . '` = ' . $key ;

				// добавление значения для подстановки в SQL PDO
				$values[ $key ] = $value ;
			}

			// возврат, если условия отбора записей по первичному ключу не составлены
			if ( empty( $primary_where ) ) {
				return false ;
			}

			/**
			* @var string $sql SQL-запрос
			*/
			$sql = "
DELETE
	`t1`.*
FROM
	`{$entityName}` AS `t1`
WHERE
	( " . implode( " ) AND ( " , $primary_where ) . " )
			" ;

			/**
			* @var PDOStatement $sth подготовленный к выполнению SQL-запрос
			*/
			$sth = $this->dbh->prepare( $sql ) ;

			// выполнение SQL-запроса с подстановкой аргументов
			$sth->execute( $values ) ;

			// закрыть курсор
			$sth->closeCursor( ) ;

			return true ;
		}

		/**
		* Обновление записи
		* @return boolean
		*/
		public function update( ) {
			/**
			* @var string $entityName имя сущности
			*/
			$entityName = $this->entityName( ) ;

			/**
			* @var array $primary_where условия удаления записи по первичному ключу
			*/
			$primary_where = array( ) ;

			/**
			* @var array $values значения для подстановки
			*/
			$values = array( ) ;

			/**
			* @var array $set поля записи для обновления
			*/
			$set = array( ) ;

			/** просмотр столбцов
			* @var int $i счётчик цикла
			* @var array $column данные столбца
			*/
			foreach ( $this->columns( ) as $i => $column ) {
				/** пропуск столбца, если значение HTTP-аргумента - пустая строка
				* @var mixed $value значение HTTP-аргумента
				*/
				switch ( $value = $this->arg( $column[ 'name' ] ) ) {
					case '' : {
						continue 2 ;
					}
				}

				/**
				* ключ для подстановки значений
				*/
				$key = ':' . $column[ 'name' ] ;

				// столбец - часть первичного ключа
				switch ( $column[ 'key' ] ) {
					case 'pri' : {
						// добавление условия отбора записей по первичному ключу
						$primary_where[] = '`t1`.`' . $column[ 'name' ] . '` = ' . $key ;

						// добавление значения для подстановки
						$values[ $key ] = $value ;

						// пропуск столбца
						continue 2 ;
					}
				}

				// добавление шполя для обновления
				$set[] = '`t1`.`' . $column[ 'name' ] . '` := ' . $key ;

				// добавление значения для подстановки
				$values[ $key ] = $value ;
			}

			// пропуск, если не найдено полей для обновления
			if ( empty( $set ) ) {
				return false ;
			}

			// пропуск, если не найдено условий для выполнения
			if ( empty( $primary_where ) ) {
				return false ;
			}

			// SQL-запрос для обновления
			$sql = "
UPDATE
	`{$entityName}` AS `t1`
SET
	" . implode( ' , ' , $set ) . "
WHERE
	( " . implode( " ) AND ( " , $primary_where ) . " )
			" ;

			/** подготовка SQL-запроса
			* @var PDOStatement $sth подготовленный SQL-запрос
			*/
			$sth = $this->dbh->prepare( $sql ) ;

			// выполнение SQL-запросас подстановкой значений
			$sth->execute( $values ) ;

			// закрытие курсора
			$sth->closeCursor( ) ;

			return true ;
		}

		/**
		* Создание записи
		* @return boolean
		*/
		public function create( ) {
			/**
			* @var string $entityName имя сущности
			*/
			$entityName = $this->entityName( ) ;

			/**
			* @var array $values значения для подстановки
			*/
			$values = array( ) ;

			/**
			* @var array $set поля записи для обновления
			*/
			$set = array( ) ;

			/** просмотр столбцов
			* @var int $i счётчик цикла
			* @var array $column данные столбца
			*/
			foreach ( $this->columns( ) as $i => $column ) {
				// пропуск столбца, если это часть первичного ключа
				switch ( $column[ 'key' ] ) {
					case 'pri' : {
						continue 2 ;
					}
				}

				/** пропуск столбца, если значение HTTP-аргумента - пустая строка
				* @var mixed $value значение HTTP-аргумента
				*/
				switch ( $value = $this->arg( $column[ 'name' ] ) ) {
					case '' : {
						continue 2 ;
					}
				}

				/**
				* ключ для подстановки значений
				*/
				$key = ':' . $column[ 'name' ] ;

				// добавление шполя для обновления
				$set[] = '`t1`.`' . $column[ 'name' ] . '` := ' . $key ;

				// добавление значения для подстановки
				$values[ $key ] = $value ;
			}

			// возврат, если не найдены столбцы для подстановки для создания записи
			if ( empty( $set ) ) {
				return false ;
			}

			/**
			* @var string $sql SQL-запрос
			*/
			$sql = "
INSERT INTO
	`{$entityName}`
SET
	" . implode( ' , ' , $set ) ;

			/** подготовка SQL-запроса
			* @var PDOStatement $sth подготовленный SQL-запрос
			*/
			$sth = $this->dbh->prepare( $sql ) ;

			// выполнение SQL-запросас подстановкой значений
			$sth->execute( $values ) ;

			// закрытие курсора
			$sth->closeCursor( ) ;

			return true ;
		}

		/**
		* получение списка записей сущности класса
		* @param boolean $select_paged получать данные постранично
		* @return array
		*/
		public function all( $select_paged = true ) {
			/**
			* @var array $data результат выполнения метода
			* page_size - количество записей на странице
			* page_current - текущая страница
			* result - список записей с дополнительной информацией о сущности
			*/
			$data = array(
				'page_size' => $this->app->config[ 'ctrl' ][ 'page_size' ] ,
				'page_current' => intval( $this->arg( 'page' ) ) ,
				'result' => array( )
			) ;

			// номер первой записи в сущности запроса для вывода
			$data[ 'record_current' ] = $data[ 'page_current' ] * $data[ 'page_size' ] ;

			/**
			* @var string $entityName имя сущности
			*/
			$entityName = $this->entityName( ) ;

			/**
			* @var string $sql SQL-запрос
			*/
			$sql = ' SELECT ' ;

			// подсчитывать общее количество записей при постраничной разбивке ответов
			if ( $select_paged ) {
				$sql .= ' SQL_CALC_FOUND_ROWS ' ;
			}

			$sql .= "
	`t1`.*
FROM
	`{$entityName}` AS `t1`
			" ;

			if ( $select_paged ) {
				// номер текущего столбца для упорядочения
				$data[ 'order_col' ] = intval( $this->arg( 'order_col' ) ) ;

				if ( ! empty( $data[ 'order_col' ] ) ) {
					// определение направления упорядочения
					if ( $this->arg( 'order_arr' ) > 0 ) {
						$order_arr = ' DESC ' ;
						$data[ 'order_arr' ] = 0 ;
					} else {
						$order_arr = ' ASC ' ;
						$data[ 'order_arr' ] = 1 ;
					}

					$sql .= "
ORDER BY
	{$data[ 'order_col' ]} {$order_arr}
					" ;
				}

				// ограничение вывода по страницам
				$sql .= "
LIMIT {$data[ 'record_current' ]} , {$data[ 'page_size' ]}
				" ;
			}

			// список записей после отбора
			$data[ 'result' ] = $this->dbh->query( $sql )->fetchAll( \PDO::FETCH_ASSOC ) ;

			// общее количество записей после отбора
			$data[ 'record_count' ] = $this->dbh->query('SELECT found_rows( ) AS `count`' )->fetchColumn( ) ;

			// общее количество страниц
			$data[ 'page_count' ] = ceil( $data[ 'record_count' ] / $data[ 'page_size' ] ) ;

			// список страниц
			$data[ 'pager' ] = range( 1 , $data[ 'page_count' ] ) ;

			/** информация о сущности
			* name - имя сущности
			* comment - комментарий к сущности
			* header - столбцы сущности
			*/
			$data[ 'table' ] = array(
				'name' => $entityName ,
				'comment' => $this->dbh->query( "
SELECT
	`t1`.`TABLE_COMMENT` AS `comment`
FROM
	`INFORMATION_SCHEMA`.`TABLES` AS `t1`
WHERE
	( `t1`.`TABLE_NAME` = '{$entityName}' ) AND
	( `t1`.`TABLE_SCHEMA` = database( ) )
				" )->fetchColumn( ) ,
				'header' => $this->columns( )
			) ;

			return $data ;
		}
	}