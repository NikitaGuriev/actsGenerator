<?php

/**
 * Блок логирования ошибок PHP
 */
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/error.log');

/**
 * Блок определения констант
 */
define('CLASSES', [
  'Application',
  'BX24',
  'debugger/Debugger',
  'crest/CRestPlus',
]);

/**
 * Автозагрузчик классов
 * @param string $class Название класса
 * @return void
 */
spl_autoload_register(function () {
  foreach (CLASSES as $cClassPath) {
    require_once $cClassPath . '.php';
  }
});

$App = new actsGenerator\libs\Application();
$BX24 = new actsGenerator\libs\BX24();
