<?php
        use Noodlehaus\Config ;
        use SimpleCrud\SimpleCrud ;

        $config = new Config( CONFIG_FILE_NAME ) ;
        $pdo = new PDO(
                $config[ 'db' ][ 'dsn' ] ,
                $config[ 'db' ][ 'user' ] ,
                $config[ 'db' ][ 'password' ]
        ) ;
        $pdo->query( 'SET names ' . $config[ 'db' ][ 'charset' ] ) ;

        $dbh = new SimpleCrud( $pdo ) ;
