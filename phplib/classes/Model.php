<?php
	namespace Application ;

	class Model {
		protected $app ;
		protected $creator ;
		protected $dbh ;
		protected $_entityName ;
		protected $_className ;

		public function __construct( $creator = null ) {
			$this->creator = $creator ;
			$this->app = $this->creator->creator ;
			$this->dbh = $this->app->dbh ;
		}

		protected function arg( $name ) {
			return $this->creator->arg( $name ) ;
		}

		protected function className( ) {
			if ( ! empty( $this->_className ) ) {
				return $this->_className ;
			}

			$this->_className = ( new \ReflectionClass( $this ) )->getShortName( ) ;

			return $this->_className ;
		}

		protected function entityName( ) {
			if ( ! empty( $this->_entityName ) ) {
				return $this->_entityName ;
			}

			$className = $this->className( ) ;
			$this->_entityName = strToLower( $className ) ;

			return $this->_entityName ;
		}

		protected function columns( ) {
			$entityName = $this->entityName( ) ;
			$result = $this->dbh->query( "
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

			return $result ;
		}

		public function action( ) {
			switch ( $action = $this->arg( 'action' ) ) {
				case 'create' :
				case 'update' :
				case 'delete' : {
					$this->$action( ) ;
					$this->creator->creator->router->redirect_back( ) ;
				}
				default : {
					return $action ;
				}
			}
		}

		public function delete( ) {
			$entityName = $this->entityName( ) ;
			$primary_where = array( ) ;
			$values = array( ) ;

			foreach ( $this->columns( ) as $i => $column ) {
				$value = $this->arg( $column[ 'name' ] ) ;

				if ( $value == '' ) {
					continue ;
				}

				$key = ':' . $column[ 'name' ] ;

				if ( $column[ 'key' ] != 'pri' ) {
					continue ;
				}

				$primary_where[] = '`t1`.`' . $column[ 'name' ] . '` = ' . $key ;
				$values[ $key ] = $value ;
			}

			if ( empty( $primary_where ) ) {
				return false ;
			}

			$sql = "
DELETE
	`t1`.*
FROM
	`{$entityName}` AS `t1`
WHERE
	( " . implode( " ) AND ( " , $primary_where ) . " )
			" ;

			$sth = $this->dbh->prepare( $sql ) ;
			$sth->execute( $values ) ;
			$sth->closeCursor( ) ;

			return true ;
		}

		public function update( ) {
			$entityName = $this->entityName( ) ;
			$set = array( ) ;
			$values = array( ) ;
			$primary_where = array( ) ;

			foreach ( $this->columns( ) as $i => $column ) {
				$value = $this->arg( $column[ 'name' ] ) ;

				if ( $value == '' ) {
					continue ;
				}

				$key = ':' . $column[ 'name' ] ;

				if ( $column[ 'key' ] == 'pri' ) {
					$primary_where[] = '`t1`.`' . $column[ 'name' ] . '` = ' . $key ;
					$values[ $key ] = $value ;

					continue ;
				}

				$set[] = '`t1`.`' . $column[ 'name' ] . '` := ' . $key ;
				$values[ $key ] = $value ;
			}

			if ( empty( $set ) ) {
				return false ;
			}
			if ( empty( $primary_where ) ) {
				return false ;
			}

			$sql = "
UPDATE
	`{$entityName}` AS `t1`
SET
	" . implode( ' , ' , $set ) . "
WHERE
	( " . implode( " ) AND ( " , $primary_where ) . " )
			" ;

			$sth = $this->dbh->prepare( $sql ) ;
			$sth->execute( $values ) ;
			$sth->closeCursor( ) ;

			return true ;
		}

		public function create( ) {
			$entityName = $this->entityName( ) ;
			$set = array( ) ;
			$values = array( ) ;

			foreach ( $this->columns( ) as $i => $column ) {
				if ( $column[ 'key' ] == 'pri' ) {
					continue ;
				}

				$value = $this->arg( $column[ 'name' ] ) ;

				if ( $value == '' ) {
					continue ;
				}

				$key = ':' . $column[ 'name' ] ;
				$set[] = '`' . $column[ 'name' ] . '` := ' . $key ;
				$values[ $key ] = $value ;
			}

			if ( empty( $set ) ) {
				return false ;
			}

			$sql = "
INSERT INTO
	`{$entityName}`
SET
	" . implode( ' , ' , $set ) ;

			$sth = $this->dbh->prepare( $sql ) ;
			$sth->execute( $values ) ;
			$sth->closeCursor( ) ;

			return true ;
		}

		public function all( $select_paged = true ) {
			$data = array(
				'page_size' => $this->app->config[ 'ctrl' ][ 'page_size' ] ,
				'page_current' => intval( $this->arg( 'page' ) ) ,
				'result' => array( )
			) ;
			$data[ 'record_current' ] = $data[ 'page_current' ] * $data[ 'page_size' ] ;
			$entityName = $this->entityName( ) ;

			$sql = ' SELECT ' ;

			if ( $select_paged ) {
				$sql .= ' SQL_CALC_FOUND_ROWS ' ;
			}

			$sql .= "
	`t1`.*
FROM
	`{$entityName}` AS `t1`
			" ;

			if ( $select_paged ) {
				$data[ 'order_col' ] = intval( $this->arg( 'order_col' ) ) ;

				if ( ! empty( $data[ 'order_col' ] ) ) {
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

				$sql .= "
LIMIT {$data[ 'record_current' ]} , {$data[ 'page_size' ]}
				" ;
			}

			$data[ 'result' ] = $this->dbh->query( $sql )->fetchAll( \PDO::FETCH_ASSOC ) ;
			$data[ 'record_count' ] = $this->dbh->query('SELECT found_rows( ) AS `count`' )->fetchColumn( ) ;
			$data[ 'page_count' ] = ceil( $data[ 'record_count' ] / $data[ 'page_size' ] ) ;
			$data[ 'pager' ] = range( 1 , $data[ 'page_count' ] ) ;

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