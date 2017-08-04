<?php
	/**
	* Программа для запуска пакета Application с текущими переменными окружения и HTTP-запросом
	* @author Shatrov Aleksej <mail@ashatrov.ru>
	*/

	// Подключение инициализирующей программы
	require_once( '../phplib/init.php' ) ;

	/**
	* @var Application\Application объект программы
	*/
	$app = new Application\Application( ) ;

	// выполнение программы
	$app->execute( ) ;
