<?php
/**
 * The test database settings. These get merged with the global settings.
 *
 * This environment is primarily used by unit tests, to run on a controlled environment.
 */

return array(
		'default' => array(
				'type' 		  => 'mysqli',
				'connection'  => array(
						'hostname'   => 'otlp01.olympus-global.com',
						'port'		 => '3306',
						'username'   => 'olympus',
						'password'   => 'oL8eS6hz',
						'database'	 => 'onaka',
				),
		),
		'profiling'		=> true,
		'charset'		=> 'utf8',
);
