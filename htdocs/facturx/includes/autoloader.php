<?php
spl_autoload_register(function ($class_name) {

	//$preg_match = preg_match('/^Smalot\\\PhpSpreadsheet\\\/', $class_name);
	$preg_match = preg_match('/^Smalot\\\PdfParser\\\/', $class_name);

	if (1 === $preg_match) {
		$class_name = preg_replace('/\\\/', '/', $class_name);
		$class_name = preg_replace('/^Smalot\\/PdfParser\\//', '', $class_name);
		require_once __DIR__ . '/smalot/pdfparser/src/Smalot/PdfParser/' . $class_name . '.php';
	} else {
		$preg_match = preg_match('/^Atgp\\\FacturX\\\/', $class_name);

		if (1 === $preg_match) {
			$class_name = preg_replace('/\\\/', '/', $class_name);
			$class_name = preg_replace('/^Atgp\\/FacturX\\//', '', $class_name);
			require_once __DIR__ . '/atgp/factur-x/src/' . $class_name . '.php';
		} else {
			$preg_match = preg_match('/^setasign\\\Fpdi\\\/', $class_name);

			if (1 === $preg_match) {
				$class_name = preg_replace('/\\\/', '/', $class_name);
				$class_name = preg_replace('/^setasign\\/Fpdi\\//', '', $class_name);
				require_once __DIR__ . '/setasign/fpdi/src/' . $class_name . '.php';
			}
		}
	}
});
