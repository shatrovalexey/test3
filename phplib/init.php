<?php
	/** программа инициализации
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	// включить отображение всех ошибок и предупреждений
	ini_set( 'display_errors' , E_ALL ) ;

	/**
	* @const string файл настроек
	*/

	define( 'CONFIG_FILE_NAME' , __DIR__ . '/config.json' ) ;

	// автозагрузка установленных composer модулей
	require_once( __DIR__ . '/vendor/autoload.php' ) ;
