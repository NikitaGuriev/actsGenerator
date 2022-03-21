<?php

use actsGenerator\libs\crest\CRestPlus as CRP;

/**
 * Блок проверки ID портала
 */
if ($_REQUEST['member_id'] != '8cf76f3d5a60a64a0604b7bdacb6db4e') {
  http_response_code(403);
  die();
}

/**
 * Блок логирования ошибок PHP
 */
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/error.log');

/** Подключение автолоадера */
require_once __DIR__ . '/libs/autoloader.php';

/**
 * Блок определения констант
 */
define('DOMAIN', $_REQUEST['DOMAIN']); // Используется в JavaScript
define(
  'PORTAL_URL',
  ($_REQUEST['PROTOCOL'] == '1' ? 'https://' : 'http://') . DOMAIN
);
define('APP_NAME', 'Генератор актов приёма-передачи');

/**
 * Блок определения триггера установки приложения
 */
$installedTrigger = false; // Триггер "Приложение успешно установлено"
if (
  isset($_REQUEST['PLACEMENT_OPTIONS']) &&
  $_REQUEST['PLACEMENT_OPTIONS'] == '{"install_finished":"Y"}'
) {
  $installedTrigger = true;
}

/**
 * Блок алгоритма работы
 */
require_once __DIR__ . '/view/index.php';
