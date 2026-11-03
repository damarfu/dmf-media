<?php
declare(strict_types=1);

/**
 * Ejecutor de pruebas de DMF Media.
 *
 * Uso: php tests/run.php
 */

require_once __DIR__ . '/bootstrap.php';

foreach ( glob( __DIR__ . '/*Test.php' ) as $file ) {
	require_once $file;
}

$total  = 0;
$failed = 0;

foreach ( $GLOBALS['dmf_tests'] as [ $name, $callback ] ) {
	dmf_reset_state();

	$total++;

	try {
		$callback();
		echo 'OK   ' . $name . PHP_EOL;
	} catch ( Throwable $exception ) {
		$failed++;
		echo 'FAIL ' . $name . PHP_EOL;
		echo '     ' . $exception->getMessage() . PHP_EOL;
	}
}

printf(
	PHP_EOL . '%d pruebas, %d aserciones, %d fallos' . PHP_EOL,
	$total,
	(int) $GLOBALS['dmf_assertions'],
	$failed
);

exit( 0 === $failed ? 0 : 1 );
