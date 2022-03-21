<?php

use actsGenerator\libs\crest\CRestPlus as CRP;
use actsGenerator\libs\debugger\Debugger as DEB;

/**
 * Блок определения констант
 */
define('LOGGING', false);
define(
  'SPACES_989',
  '_____________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________'
);

/**
 * Свойства товара
 */
define('AP_LITERA_ID', 'PROPERTY_' . '230');
define('AP_STATUS', 'PROPERTY_' . '105');

/**
 * Поля сделки
 */
define('DEAL_PRODUCT_ID', 'UF_CRM_1526044192');
define('DEAL_EQUITY_AGREEMENT_NUMBER', 'UF_CRM_1524806431');
define('DEAL_EQUITY_AGREEMENT_DATE', 'UF_CRM_1613469880');
define('DEAL_ROSREESTR_REG_DATE', 'UF_CRM_1606894473');
define('DEAL_ROSREESTR_REG_NUMBER', 'UF_CRM_1613468444');
define('DEAL_LITER_NAME', 'UF_CRM_1528233857');
define('DEAL_BALCONY_AREA', 'UF_CRM_1523820801');
define('DEAL_AREA_WITHOUT_BALCONY', 'UF_CRM_1613471475');
define('DEAL_FACT_AREA_WITHOUT_BALCONY', 'UF_CRM_1613549729');

/**
 * Информация о квартире
 */
define('DEAL_AP_NUMBER', 'UF_CRM_1523820728');
define('DEAL_AP_FLOOR', 'UF_CRM_1523820834');

/**
 * Поля контакта
 */
define('C_BIRTH_PLACE', 'UF_CRM_1605784046543');

/**
 * Паспорт
 */
define('C_PASSPORT_SERIES', 'UF_CRM_1605098469');
define('C_PASSPORT_NUMBER', 'UF_CRM_1606816190');
define('C_PASSPORT_ISSUE_DATE', 'UF_CRM_1526565067');
define('C_PASSPORT_ISSUED_BY', 'UF_CRM_1526565084');
define('C_PASSPORT_DEPARTMENT_CODE', 'UF_CRM_1606487289');
define('C_PASSPORT_REG_PLACE', 'UF_CRM_1526565206');

/**
 * Поля УС "Жилые комплексы"
 */
define('UL_CONTRACT_ADDRESS', 'PROPERTY_' . '226');
define('UL_REQUISITES_FULL', 'PROPERTY_' . '237');
define('UL_REQUISITES_SHORT', 'PROPERTY_' . '238');
define('UL_COMMISSIONING_APPROVAL_NUMBER', 'PROPERTY_' . '234');
define('UL_COMMISSIONING_APPROVAL_DATE', 'PROPERTY_' . '235');

/**
 * Определение стилей PHPWord
 */
define('PW_FONT_FAMILY', 'Times New Roman');
define('PW_STANDARD_FONT', ['name' => PW_FONT_FAMILY]);
define('PW_BOLD_FONTS', ['bold' => true, 'name' => PW_FONT_FAMILY]);
define('PW_CENTER_ALIGNMENT', ['alignment' => 'center']);
define('PW_BOTH_ALIGNMENT', ['alignment' => 'both']);
define('PW_P_BOTH_INDENT', [
  'alignment' => 'both',
  // 'indent' => 0.79,
]);
define('PW_BOLD_CENTER', [
  'bold' => true,
  'align' => 'center',
  'name' => PW_FONT_FAMILY,
]);
define('PW_CENTER', ['align' => 'center']);

/** Подключение автолоадера PHPWord */
require_once __DIR__ . '/vendor/autoload.php';

DEB::wtl(
  $_REQUEST,
  '$_REQUEST' . ' ' . __LINE__ . ' строка (phpWord.php)',
  'phpWord_$_REQUEST'
);

/**
 * Получение данных из УС "Жилые комплексы"
 */
$callHCLists = CRP::call('lists.element.get', [
  'IBLOCK_TYPE_ID' => 'lists',
  'IBLOCK_ID' => 45,
  'FILTER' => [
    '=' . AP_LITERA_ID => $_REQUEST['data']['SELECTED_LITERA'],
  ],
]);
DEB::wtl(
  $callHCLists,
  '$callHCLists' . ' ' . __LINE__ . ' строка (phpWord.php)',
  'phpWord_$callHCLists'
);
$HCULData = $callHCLists['result'][0];

/**
 * Блок получения ЖК и литеров товаров
 */
$sectionsCall = CRP::callBatchList('crm.productsection.list', [
  'select' => ['ID', 'SECTION_ID', 'NAME'],
]);
DEB::wtl(
  $sectionsCall,
  '$sectionsCall' . ' ' . __LINE__ . ' строка (phpWord.php)',
  'phpWord_$sectionsCall'
);

/** [SaHCLitera] Парсинг ID Литеры = Имя Литеры */
foreach ($sectionsCall['result']['result'] as $scPackages) {
  foreach ($scPackages as $scItems) {
    $secAll[] = $scItems;
  }
}
foreach ($secAll as $scValues) {
  foreach ($secAll as $scValue) {
    if (
      $scValues['SECTION_ID'] != $scValue['ID'] &&
      !is_null($scValues['SECTION_ID'])
    ) {
      $SaHCLitera[$scValues['ID']] = $scValues['NAME'];
    }
  }
}
DEB::wtl(
  $SaHCLitera,
  '$SaHCLitera' . ' ' . __LINE__ . ' строка (phpWord.php)',
  'phpWord_$SaHCLitera'
);

/**
 * Блок получения товаров
 */
$callProducts = CRP::callBatchList('crm.product.list', [
  'filter' => [
    AP_STATUS => ['79'],
    'SECTION_ID' => $_REQUEST['data']['SELECTED_LITERA'],
  ],
  'select' => ['SECTION_ID'],
]);
DEB::wtl(
  $callProducts,
  '$callProducts' . ' ' . __LINE__ . ' строка (phpWord.php)',
  'phpWord_$callProducts'
);

if ($callProducts != 'error') {
  foreach ($callProducts['result']['result'] as $cpPackage) {
    foreach ($cpPackage as $productData) {
      $productsIds[] = $productData['ID'];
    }
  }

  DEB::wtl(
    $productsIds,
    '$productsIds' . ' ' . __LINE__ . ' строка (phpWord.php)',
    'phpWord_$productsIds'
  );

  /**
   * Получение сделок
   */
  $callDeals = CRP::callBatchList('crm.deal.list', [
    'filter' => [
      DEAL_PRODUCT_ID => $productsIds,
    ],
    'select' => [
      DEAL_PRODUCT_ID,
      DEAL_EQUITY_AGREEMENT_DATE,
      DEAL_EQUITY_AGREEMENT_NUMBER,
      DEAL_ROSREESTR_REG_DATE,
      DEAL_ROSREESTR_REG_NUMBER,
      DEAL_LITER_NAME,
      DEAL_AP_NUMBER,
      DEAL_AP_FLOOR,
      DEAL_BALCONY_AREA,
      DEAL_AREA_WITHOUT_BALCONY,
      DEAL_FACT_AREA_WITHOUT_BALCONY,
    ],
  ]);
  DEB::wtl(
    $callDeals,
    '$callDeals' . ' ' . __LINE__ . ' строка (phpWord.php)',
    'phpWord_$callDeals'
  );

  if ($callDeals != 'error') {
    foreach ($callDeals['result']['result'] as $cdPackage) {
      foreach ($cdPackage as $dealData) {
        $contactsGetBatch['dealId_' . $dealData['ID']] = [
          'method' => 'crm.deal.contact.items.get',
          'params' => [
            'id' => $dealData['ID'],
          ],
        ];
      }
    }
    DEB::wtl(
      $contactsGetBatch,
      '$contactsGetBatch' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$contactsGetBatch'
    );
    $getDealContacts = CRP::callBatch($contactsGetBatch);
    DEB::wtl(
      $getDealContacts,
      '$getDealContacts' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$getDealContacts'
    );
    foreach (
      $getDealContacts['result']['result']
      as $UFDealId => $dealContacts
    ) {
      foreach ($dealContacts as $dealContact) {
        $dealId = explode('_', $UFDealId)[1];
        $contactsWL[] = $dealContact['CONTACT_ID'];
        $dealContactsArr[$dealId] = $dealContact['CONTACT_ID'];
      }
    }
    DEB::wtl(
      $contactsWL,
      '$contactsWL' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$contactsWL'
    );
    DEB::wtl(
      $dealContactsArr,
      '$dealContactsArr' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$dealContactsArr'
    );

    /**
     * Блок получения информации по контактам
     */
    $getContactsData = CRP::callBatchList('crm.contact.list', [
      'filter' => [
        'ID' => $contactsWL,
      ],
      'select' => [
        'NAME',
        'LAST_NAME',
        'SECOND_NAME',
        'BIRTHDATE',
        'PHONE',
        C_BIRTH_PLACE,
        C_PASSPORT_SERIES,
        C_PASSPORT_NUMBER,
        C_PASSPORT_ISSUE_DATE,
        C_PASSPORT_ISSUED_BY,
        C_PASSPORT_DEPARTMENT_CODE,
        C_PASSPORT_REG_PLACE,
      ],
    ]);
    DEB::wtl(
      $getContactsData,
      '$getContactsData' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$getContactsData'
    );
    foreach ($getContactsData['result']['result'] as $gcdPackage) {
      foreach ($gcdPackage as $contactData) {
        foreach ($dealContactsArr as $dcaDealId => $dcaContactId) {
          if ($dcaContactId == $contactData['ID']) {
            $dealContactsData[$dcaDealId][] = $contactData;
          }
        }
      }
    }
    DEB::wtl(
      $dealContactsData,
      '$dealContactsData' . ' ' . __LINE__ . ' строка (phpWord.php)',
      'phpWord_$dealContactsData'
    );

    /**
     * Зполнение Word-файла
     */
    foreach ($callDeals['result']['result'] as $cdPackage) {
      foreach ($cdPackage as $dealData) {
        /**
         * Инициализация библиотеки PHPWord
         */
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection([
          'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.5),
          'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.75),
          'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5),
          'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.25),
        ]);

        $section->addText('Акт', PW_BOLD_FONTS, PW_CENTER_ALIGNMENT);
        $section->addText(
          'приема-передачи квартиры',
          PW_BOLD_FONTS,
          PW_CENTER_ALIGNMENT
        );
        $section->addText('', PW_STANDARD_FONT, PW_CENTER_ALIGNMENT);
        $section->addText(
          'г. Краснодар                                                                                                                                        «___» ______________ 202__г.',
          PW_STANDARD_FONT
        );
        $section->addText('', PW_STANDARD_FONT, PW_CENTER_ALIGNMENT);

        /**
         * Реквизиты полные
         */
        if (!empty($HCULData[UL_REQUISITES_FULL])) {
          $fullReqArr = current($HCULData[UL_REQUISITES_FULL]);
          $paragraph1 =
            "\t" .
            $fullReqArr .
            ', именуемое в дальнейшем «Застройщик», с одной стороны, и';
        } else {
          $paragraph1 = SPACES_989;
        }

        /**
         * Реквизиты краткие
         */
        if (!empty($HCULData[UL_REQUISITES_SHORT])) {
          $paragraph12 = current($HCULData[UL_REQUISITES_SHORT]);
        } else {
          $paragraph12 =
            '___________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________';
        }

        /**
         * Номер разрешения на ввод в эксплуатацию
         */
        if (!empty($HCULData[UL_COMMISSIONING_APPROVAL_NUMBER])) {
          $commissioningApprovalNumber = current(
            $HCULData[UL_COMMISSIONING_APPROVAL_NUMBER]
          );
        } else {
          $commissioningApprovalNumber =
            '_______________________________________';
        }

        /**
         * Дата разрешения на ввод в эксплуатацию
         */
        if (!empty($HCULData[UL_COMMISSIONING_APPROVAL_DATE])) {
          $commissioningApprovalDate = current(
            $HCULData[UL_COMMISSIONING_APPROVAL_DATE]
          );
        } else {
          $commissioningApprovalDate = '__.__.____';
        }

        /**
         * Адрес для договора
         */
        if (!empty($HCULData[UL_CONTRACT_ADDRESS])) {
          $contractAddress = current($HCULData[UL_CONTRACT_ADDRESS]);
        } else {
          $contractAddress =
            '________________________________________________________________________________________________________________________________________________________________________';
        }

        /**
         * Дата ДДУ
         */
        if (!empty($dealData[DEAL_EQUITY_AGREEMENT_DATE])) {
          $equityAgreementDate = date(
            'd.m.Y',
            strtotime($dealData[DEAL_EQUITY_AGREEMENT_DATE])
          );
        } else {
          $equityAgreementDate = '__.__.____';
        }

        /**
         * Номер ДДУ
         */
        if (!empty($dealData[DEAL_EQUITY_AGREEMENT_NUMBER])) {
          $equityAgreementNumber = $dealData[DEAL_EQUITY_AGREEMENT_NUMBER];
        } else {
          $equityAgreementNumber = '________________';
        }

        /**
         * Номер регистрации в РосРеестре
         */
        if (!empty($dealData[DEAL_ROSREESTR_REG_NUMBER])) {
          $rosreestrRegNumber = $dealData[DEAL_ROSREESTR_REG_NUMBER];
        } else {
          $rosreestrRegNumber = '__________________________________';
        }

        /**
         * Дата регистрации в РосРеестре
         */
        if (!empty($dealData[DEAL_ROSREESTR_REG_DATE])) {
          $rosreestrRegDate = date(
            'd.m.Y',
            strtotime($dealData[DEAL_ROSREESTR_REG_DATE])
          );
        } else {
          $rosreestrRegDate = '__.__.____';
        }

        /**
         * Блок получения информации по контактам
         */

        $FIO = '_______ ________ _______';
        $FIOForFile = '';
        $birthDate = '__.__.____';
        $birthPlace = '____________________________________';
        $passportSeries = '__ __';
        $passportNumber = '______';
        $passportIssueDate = '__.__.____';
        $passportIssuedBy =
          '___________________________________________________';
        $passportDepartmentCode = '___-___';
        $passportPassportRegPlace =
          '_________________________________________________________________________';
        $phoneNumbers = '_-___-___-__-__, ';

        /**
         * Получение информации от контакта
         */
        foreach ($dealContactsData as $dcdDealId => $dcdContactData) {
          if ($dcdDealId == $dealData['ID']) {
            /**
             * Формирование ФИО
             */
            if (!empty($dcdContactData[0]['LAST_NAME'])) {
              $FIO = $dcdContactData[0]['LAST_NAME'] . ' ';
              $FIOForFile .= $dcdContactData[0]['LAST_NAME'] . '_';
            }
            if (!empty($dcdContactData[0]['NAME'])) {
              $FIO .= $dcdContactData[0]['NAME'] . ' ';
              $FIOForFile .= mb_substr($dcdContactData[0]['NAME'], 0, 1) . '.';
            }
            if (!empty($dcdContactData[0]['SECOND_NAME'])) {
              $FIO .= $dcdContactData[0]['SECOND_NAME'] . ' ';
              $FIOForFile .=
                mb_substr($dcdContactData[0]['SECOND_NAME'], 0, 1) . '.';
            }
            $FIO = trim($FIO);

            /**
             * Формирование номеров телефонов
             */
            if (!empty($dcdContactData[0]['PHONE'])) {
              $phoneNumbers = '';
              foreach ($dcdContactData[0]['PHONE'] as $dcdPhones) {
                $phoneNumbers .= $dcdPhones['VALUE'] . ', ';
              }
            }

            /**
             * Дата рождения
             */
            if (!empty($dcdContactData[0]['BIRTHDATE'])) {
              $birthDate = date(
                'd.m.Y',
                strtotime($dcdContactData[0]['BIRTHDATE'])
              );
            }

            /**
             * Место рождения
             */
            if (!empty($dcdContactData[0][C_BIRTH_PLACE])) {
              $birthPlace = $dcdContactData[0][C_BIRTH_PLACE];
            }

            /**
             * Серия паспорта
             */
            if (!empty($dcdContactData[0][C_PASSPORT_SERIES])) {
              $passportSeries = $dcdContactData[0][C_PASSPORT_SERIES];
            }

            /**
             * Номер паспорта
             */
            if (!empty($dcdContactData[0][C_PASSPORT_NUMBER])) {
              $passportNumber = $dcdContactData[0][C_PASSPORT_NUMBER];
            }

            /**
             * Дата выдачи паспорта
             */
            if (!empty($dcdContactData[0][C_PASSPORT_ISSUE_DATE])) {
              $passportIssueDate = date(
                'd.m.Y',
                strtotime($dcdContactData[0][C_PASSPORT_ISSUE_DATE])
              );
            }

            /**
             * Кем выдан паспорт
             */
            if (!empty($dcdContactData[0][C_PASSPORT_ISSUED_BY])) {
              $passportIssuedBy = $dcdContactData[0][C_PASSPORT_ISSUED_BY];
            }

            /**
             * Код подразделения
             */
            if (!empty($dcdContactData[0][C_PASSPORT_DEPARTMENT_CODE])) {
              $passportDepartmentCode =
                $dcdContactData[0][C_PASSPORT_DEPARTMENT_CODE];
            }

            /**
             * Код подразделения
             */
            if (!empty($dcdContactData[0][C_PASSPORT_REG_PLACE])) {
              $passportPassportRegPlace =
                $dcdContactData[0][C_PASSPORT_REG_PLACE];
            }
          }
        }

        $paragraph2 =
          "\t" .
          'Гр. РФ ' .
          $FIO .
          ', ' .
          $birthDate .
          ' года рождения, место рождения: ' .
          $birthPlace .
          ', паспорт гр. Российской Федерации серии ' .
          $passportSeries .
          ' № ' .
          $passportNumber .
          ', выдан ' .
          $passportIssueDate .
          ' г. ' .
          $passportIssuedBy .
          ', код подразделения ' .
          $passportDepartmentCode .
          ', зарегистрирован(а) по адресу: ' .
          $passportPassportRegPlace .
          ', тел.: ' .
          $phoneNumbers .
          'именуемый(ая) в дальнейшем «Дольщик», с другой стороны, составили настоящий акт о нижеследующем:';
        $paragraph3 =
          "\t" .
          '1. Во исполнение договора участия в долевом строительстве многоквартирного дома № ' .
          $equityAgreementNumber .
          ' от ' .
          $equityAgreementDate .
          ' г., зарегистрированного Управлением Федеральной службы государственной регистрации, кадастра и картографии по Республике Адыгея ' .
          $rosreestrRegDate .
          ' г., номер записи регистрации № ' .
          $rosreestrRegNumber .
          ' (далее по тексту – «Договор»), Застройщик передает, а Дольщик принимает в собственность на основании Разрешения на ввод объекта в эксплуатацию № ' .
          $commissioningApprovalNumber .
          ' от ' .
          $commissioningApprovalDate .
          ' г.' .
          ', объект долевого строительства квартиру, расположенную по адресу: ' .
          $contractAddress .
          ', имеющую следующие характеристики:';
        $paragraph5 =
          "\t" .
          '2. Застройщик подтверждает, что Квартира оплачена Дольщиком полностью.';
        $paragraph6 =
          "\t" .
          '3. Дольщик подтверждает, что Квартира полностью соответствует параметрам, указанным в Договоре. Качество строительных работ в Квартире Дольщиком проверено, услуги Застройщика выполнены в полном объеме. Дольщик осмотрел Квартиру и не имеет претензий к её качеству, параметрам, качеству инженерных коммуникаций. Ключи от Квартиры переданы Дольщику в момент подписания настоящего акта приема-передачи. Техническое состояние Квартиры на момент передачи ее Дольщику соответствует ее назначению. Дольщик не имеет материальных и иных претензий к Застройщику, связанных с качеством Квартиры.';
        $paragraph7 =
          "\t" .
          '4. Дольщик после подписания настоящего акта самостоятельно несет ответственность за риски, связанные с сохранностью и эксплуатацией Квартиры.';
        $paragraph8 =
          "\t" .
          '5. Обязательства Застройщика считаются исполненными с момента подписания Сторонами акта приема-передачи Квартиры. Стороны считают взаимные обязательства по Договору исполненными и не имеют в рамках Договора взаимных претензий.';
        $paragraph9 =
          "\t" .
          '6. Право собственности на Квартиру возникает у Дольщика с момента регистрации в Управлении Федеральной службы государственной регистрации, кадастра и картографии по Республике Адыгея.';
        $paragraph10 = '';
        $paragraph11 = 'Передал Застройщик:';
        $paragraph13 = '';
        $paragraph14 = 'Принял Дольщик:';
        $paragraph15 =
          'С инструкцией по эксплуатации объекта долевого строительства по адресу: ' .
          $contractAddress .
          ', ознакомлен и согласен, 1 экземпляр инструкции на руки получен:';
        $paragraph16 = '';
        $paragraph17 = '';
        $paragraph18 =
          '____________________________________________________________________________/_____________/';

        $section->addText($paragraph1, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph2, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph3, PW_STANDARD_FONT, PW_P_BOTH_INDENT);

        /**
         * Начало блока построения таблицы
         */
        $paragraph4Table = $section->addTable([
          'borderSize' => 6,
          'alignment' => 'center',
        ]);
        /**
         * Строка №1
         */
        $paragraph4Table->addRow();

        $paragraph4Table
          ->addCell(null, ['gridSpan' => 4])
          ->addText('Данные по Договору', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table
          ->addCell(null, ['gridSpan' => 4])
          ->addText('Фактические данные', PW_BOLD_CENTER, PW_CENTER);

        /**
         * Строка №2
         */
        $paragraph4Table->addRow();

        $paragraph4Table
          ->addCell()
          ->addText('Номер квартиры', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table->addCell()->addText('Этаж', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText('Корпус', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText(
            'Общая проектная площадь без учета площади балкона и/или лоджии',
            PW_BOLD_CENTER,
            PW_CENTER
          );
        $paragraph4Table
          ->addCell()
          ->addText('Номер квартиры', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table->addCell()->addText('Этаж', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText('Литер', PW_BOLD_CENTER, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText(
            'Общая фактическая площадь без учета площади балкона и/или лоджии по итогам технической инвентаризации',
            PW_BOLD_CENTER,
            PW_CENTER
          );

        /**
         * Строка №3
         */
        $paragraph4Table->addRow();
        $paragraph4Table
          ->addCell()
          ->addText($dealData[DEAL_AP_NUMBER], PW_STANDARD_FONT, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText($dealData[DEAL_AP_FLOOR], PW_STANDARD_FONT, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText(
            $SaHCLitera[$dealData[DEAL_LITER_NAME]],
            PW_STANDARD_FONT,
            PW_CENTER
          );
        $paragraph4Table
          ->addCell()
          ->addText(
            $dealData[DEAL_AREA_WITHOUT_BALCONY],
            PW_STANDARD_FONT,
            PW_CENTER
          );
        $paragraph4Table
          ->addCell()
          ->addText($dealData[DEAL_AP_NUMBER], PW_STANDARD_FONT, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText($dealData[DEAL_AP_FLOOR], PW_STANDARD_FONT, PW_CENTER);
        $paragraph4Table
          ->addCell()
          ->addText(
            $SaHCLitera[$dealData[DEAL_LITER_NAME]],
            PW_STANDARD_FONT,
            PW_CENTER
          );
        $paragraph4Table
          ->addCell()
          ->addText(
            $dealData[DEAL_FACT_AREA_WITHOUT_BALCONY],
            PW_STANDARD_FONT,
            PW_CENTER
          );
        /**
         * Конец блока построения таблицы
         */

        $section->addText($paragraph5, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph6, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph7, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph8, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph9, PW_STANDARD_FONT, PW_P_BOTH_INDENT);
        $section->addText($paragraph10, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph11, PW_BOLD_FONTS, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph12, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph13, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph14, PW_BOLD_FONTS, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph15, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph16, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph17, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);
        $section->addText($paragraph18, PW_STANDARD_FONT, PW_BOTH_ALIGNMENT);

        /**
         * Блок сохранения файла docx
         */
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter(
          $phpWord,
          'Word2007'
        );
        $FIOForFile = preg_replace('/:|\*|\?|"|<|>|\|/', '_', $FIOForFile);
        $FIOForFile = preg_replace('/\\\|\//', '_', $FIOForFile);
        $fileName =
          'кв._' . $dealData[DEAL_AP_NUMBER] . '_' . $FIOForFile . '.docx';
        $objWriter->save(__DIR__ . '/acts/' . $fileName);
        $fileNames[] =
          'https://darstroycrm.ru/hook/actsGenerator/acts/' . $fileName;
      }
    }

    echo json_encode([
      'status' => 'success',
      'file_name' => $fileNames,
    ]);
  } else {
    echo json_encode([
      'status' => 'deals_not_found',
    ]);
  }
} else {
  echo json_encode([
    'status' => 'products_not_found',
  ]);
}
