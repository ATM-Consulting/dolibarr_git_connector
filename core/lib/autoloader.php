<?php

spl_autoload_register(function ($class) {
	$modulePath = dirname(__DIR__, 2);
	$classPath = str_replace(['\\', '_'], '/', $class) . '.php';
	$file = $modulePath . '/class/' . $classPath;

	if (file_exists($file)) {
		require_once $file;
	}
});
