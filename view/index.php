<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Пользовательский CSS -->
  <link rel="stylesheet" href="view/css/customizeBS.css?<?= time() ?>">
  <link rel="stylesheet" href="view/css/anims.css?<?= time() ?>">
  <link rel="stylesheet" href="view/css/main.css?<?= time() ?>">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="../chess/bootstrap-4.5.2-dist/css/darstroy.css?6">
  <link rel="stylesheet" href="../bs_icons/bootstrap-icons.css">

  <title><?= APP_NAME ?></title>

  <!-- JavaScript-библиотека Битрикс24 -->
  <script src="//api.bitrix24.com/api/v1/"></script>

  <!-- Перенос значений переменных PHP в JavaScript -->
  <script>
    /**
     * Блок информации о текущем пользователе
     */
    const CURRENT_UID = <?= $App->CURRENT_UID ?>;
    const IS_CURRENT_USER_ADMIN = <?= $App->IS_CURRENT_USER_ADMIN
      ? 'true'
      : 'false' ?>;

    /** 
     * Блок информации о приложении
     */
    const INSTALLED_TRIGGER = <?= $App->INSTALLED_TRIGGER ? 'true' : 'false' ?>;
  </script>
</head>

<body class="bg-light">

  <!-- Toast -->
  <div id="toasts">
    <!-- Сюда будут вставляться toast -->
  </div>

  <!-- Навигационная панель -->
  <nav class="navbar navbar-light bg-white shadow-sm">
    <div class="container">
      <span class="navbar-brand"><em class="bi bi-file-earmark-word pr-2 text-darstroy"></em><?= APP_NAME ?></span>
    </div>
  </nav>

  <!-- Алерты -->
  <div id="alerts" class="mt-4 mx-auto w-75"></div>

  <?php if ($App->CURRENT_UID != 1028): ?>
    <div class="container mt-4">
      <div class="alert alert-danger text-center" role="alert">
        Приложение находится в стадии разработки
      </div>
    </div>
  <?php else: ?>

    <div class="container mt-4 p-4 shadow-sm rounded bg-white" id="mainData">
      <div class="row justify-content-center">
        <div class="col-auto">
          <label for="selectHC" class="text-muted">Выберите ЖК:</label>
          <?= $App->insertSelect($App->HCS, 'selectHC') ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Загрузка нескольких файлов -->
  <script src="view/js/browser.js"></script>

  <!-- Пользовательский JavaScript -->
  <script src="view/js/main.js?<?= time() ?>"></script>

  <!-- Bootstrap -->
  <script src="../chess/bootstrap-4.5.2-dist/js/jquery-3.5.1.min.js"></script>
  <script src="../chess/bootstrap-4.5.2-dist/js/popper.min.js"></script>
  <script src="../chess/bootstrap-4.5.2-dist/js/bootstrap.min.js"></script>
</body>

</html>