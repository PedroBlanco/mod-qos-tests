<?php

$fichero_prueba_1 = '/home/bwp000/php/init.test';
$init_test = false;
$http_200 = 0;
$http_429 = 0;
$http_502 = 0;
$http_resto = 0;
$total = 0;
$z = 0; // Estado no esperado
$ip_origen = '10.66.128.119';

while ( true ){
	if ( ! file_exists ( $fichero_prueba_1 ) ) {
		printf ( PHP_EOL.PHP_EOL.'--- Esperando a que exista '.$fichero_prueba_1.PHP_EOL );
	} else {
		printf ( PHP_EOL.PHP_EOL.'+++ Continuamos tests; existe '.$fichero_prueba_1.PHP_EOL );
	}
	while ( ! file_exists ( $fichero_prueba_1 ) ) {
		//$init_test = false;
		sleep ( 1 );
	}
	$init_test = true;
	for ( $i = 1; $i <= 1000 ; $i++ ){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://10.66.128.119/tests/test.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_INTERFACE, $ip_origen);
/*		curl_setopt($ch, CURLOPT_HTTPHEADER, Array (
			"mod-qos-vip: %VBEBahXT1%VBEBahXT1"
		));
 */
		$result = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
		if (!curl_errno($ch)) {
			switch ( $curl_info['http_code'] ) { 
			case 200:
				//printf($result);
				$http_200++;
				break;
			case 429:
				//printf($result);
				$http_429++;
				break;
			case 502:
				//printf($result);
				$http_502++;
				break;
			default:
				$http_resto++;
				printf ( $curl_info['http_code'] );
				//printf ( PHP_EOL.curl_error($ch).PHP_EOL );
			}
		} else {
			$z++;
			//printf ( PHP_EOL.curl_error($ch).PHP_EOL );
		}
		$total++;
		echo "\ri: $i | 200: $http_200 | 429: $http_429 | 502: $http_502 | resto: $http_resto | total: $total (¿$z?)";
		curl_close($ch);
	}
}	

