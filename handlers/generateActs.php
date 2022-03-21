<?php

/**
 * Блок логирования ошибок PHP
 */
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/error.log');

/**
 * Блок подключения модулей
 */
require_once '../libs/autoloader.php';

/**
 * Блок алгоритма работы
 */

require_once '../phpWord.php';
