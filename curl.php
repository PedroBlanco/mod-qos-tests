<?php

$init_test = false;
$http_200 = 0;
$http_429 = 0;
$http_502 = 0;
$http_resto = 0;
$total = 0;
$z = 0; // Estado no esperado


// Datos de configuracion por defecto
$fichero_prueba_1 = 'init.test';
$ip_origen = '127.0.0.1';
$url = "http://127.0.0.1/tests/test.php";
$arr_header = Array ( "mod-qos-vip: TEST1TEST2TEST3TEST4" );
$no_proxy = true;


if ( is_readable ( 'config.php' ) ) {
	include 'config.php';
}

printf ( "***** Iniciando test de mod-qos *****".PHP_EOL );
printf ( "*** - Archivo de activacion: %s".PHP_EOL, $fichero_prueba_1 );
printf ( "*** - IP de origen: %s".PHP_EOL, $ip_origen );
printf ( "*** - URL de consulta: %s".PHP_EOL, $url );
printf ( "*** - Cabeceras:".PHP_EOL);
foreach ( $arr_header as $cabecera ) {
	printf (  "***   - %s".PHP_EOL, $cabecera );
}

while ( true ){
	if ( ! file_exists ( $fichero_prueba_1 ) ) {
		printf ( PHP_EOL.PHP_EOL.'--- Esperando a que exista %s'.PHP_EOL, $fichero_prueba_1 );
	} else {
		printf ( PHP_EOL.PHP_EOL.'+++ Continuamos tests; existe %s'.PHP_EOL, $fichero_prueba_1 );
	}
	while ( ! file_exists ( $fichero_prueba_1 ) ) {
		//$init_test = false;
		sleep ( 1 );
	}
	$init_test = true;
	for ( $i = 1; $i <= 1000 ; $i++ ){
		$ch = curl_init();

		if ( $no_proxy ) {
			curl_setopt($ch, CURLOPT_PROXY, '');
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
		}
		curl_setopt($ch, CURLOPT_URL, "http://10.66.128.119/tests/test.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_INTERFACE, $ip_origen);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arr_header);
 
		$result = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
		if (!curl_errno($ch)) {
			switch ( $curl_info['http_code'] ) { 
			case 200:
				// Peticion correcta
				//printf($result);
				$http_200++;
				break;
			case 429:
				// Error por defecto de mod_qos
				//printf($result);
				$http_429++;
				break;
			case 502:
				// Error que devuelve el proxy cuando no puede hacer conexion
				//printf($result);
				$http_502++;
				break;
			default:
				// Resto de errores HTTP
				$http_resto++;
				//printf ( $curl_info['http_code'] );
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

