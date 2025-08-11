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
						'hostname'   => '10.236.167.228',
						'port'		 => '3306',
						'username'   => 'onaka',
						'password'   => 'VZWHhetYSaokN0ZckEuLEKjR',
						'database'	 => 'onaka_dev',
				),
		),
		'profiling'		=> true,
		'charset'		=> 'utf8',
);
