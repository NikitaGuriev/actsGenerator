window.addEventListener('load', () => {
  /**
   * Класс работы с DOM приложения
   */
  class DOMManipulations {
    /**
     * Конструктор класса
     */
    constructor() {
      this.SELECT_HC = document.getElementById('selectHC');
      this.MAIN_DATA_DIV = document.getElementById('mainData');
      this.TOASTS_DIV = document.getElementById('toasts');
      this.addEventListeners();
    }

    /**
     * Добавление обработчиков событий
     */
    addEventListeners() {
      /**
       * Выбор элемента в выпадающем списке
       */
      this.SELECT_HC.addEventListener('change', (e) => {
        if (e.target.value == 'empty') {
          this.generateToast(
              'Ошибка!',
              'ЖК не выбран. Пожалуйста, выберите ЖК и повторите попытку',
          );
        } else {
          this.SELECTED_HC = e.target.value;
          this.loadHCLiteras(e.target.value);
        }
      });
    }

    /**
     * Показать кнопку генерации отчёта
     */
    showStartButton() {
      if (this.RUN_BTN_DIV != null) {
        this.RUN_BTN_DIV.remove();
      }
      this.MAIN_DATA_DIV.insertAdjacentHTML(
          'beforeend',
          `
          <div class="row justify-content-center slideFromBot" id="runBtnDiv">
            <div class="col-auto">
              <button
                type="button"
                class="btn btn-darstroy mt-4"
                id="generateActs"
              >
                Запустить
                <span
                  class="spinner-border spinner-border-sm d-none ml-2"
                  role="status"
                  aria-hidden="true"
                  id="spinner"
                ></span>
              </button>
            </div>
          </div>
      `,
      );
      this.RUN_BTN_DIV = document.getElementById('runBtnDiv');
      this.SPINNER = document.getElementById('spinner');
      this.GENERATE_REPORT_BTN = document.getElementById('generateActs');
      this.GENERATE_REPORT_BTN.addEventListener('click', () => {
        const RESPONSE_DATA = APP.sendFetch(
            'handlers/generateActs.php',
            {
              SELECTED_HC: this.SELECTED_HC,
              SELECTED_LITERA: this.SELECTED_LITERA,
            },
            true,
        );
        RESPONSE_DATA.then((res) => {
          console.dir(res);
          if (res.status == 'success') {
            const FILE_NAMES = Object.values(res.file_name);
            multiDownload(FILE_NAMES);
          } else if (res.status == 'products_not_found') {
            this.generateToast(
                'Ошибка',
                `Квартиры со статусом "В продаже"
                в выбранном литере не найдены`,
                'error',
            );
          } else if (res.status == 'deals_not_found') {
            this.generateToast(
                'Ошибка',
                `Сделки по товарам не найдены`,
                'error',
            );
          }
        });
      });
    }

    /**
     * Получить список литер по ЖК
     * @param {string|number} HCId ID ЖК
     */
    loadHCLiteras(HCId) {
      if (this.LITERA_SELECT != null) {
        this.LITERA_SELECT.remove();
      }
      if (this.RUN_BTN_DIV != null) {
        this.RUN_BTN_DIV.remove();
      }
      const RESPONSE_DATA = APP.sendFetch('handlers/loadHCLiteras.php', HCId);
      RESPONSE_DATA.then((res) => {
        if (this.LITERA_SELECT != null) {
          this.LITERA_SELECT.remove();
        }
        this.MAIN_DATA_DIV.insertAdjacentHTML('beforeend', res.outputHTML);
        this.LITERA_SELECT = document.getElementById('literasSelect');
        this.SELECT_LITERA = document.getElementById('literasList');
        this.SELECT_LITERA.addEventListener('change', (e) => {
          if (e.target.value == 'empty') {
            this.generateToast(
                'Ошибка!',
                `Литер не выбран.
                Пожалуйста, выберите литер и повторите попытку`,
            );
          } else {
            this.SELECTED_LITERA = e.target.value;
            this.showStartButton();
          }
        });
      });
    }

    /**
     * Сгенерировать toast
     * @param {string} toastHeader Заголовок toast
     * @param {string} toastContent Содержимое окна toast
     * @param {string} toastType Тип toast
     */
    generateToast(
        toastHeader = 'Ошибка',
        toastContent = `Произошла непредвиденная ошибка.
        Пожалуйста, повторите попытку позже,
        либо обратитесь к разработчику приложения`,
        toastType = 'error',
    ) {
      let toastColor = '';
      if (toastType == 'error') {
        toastColor = 'text-danger';
      } else if (toastType == 'success') {
        toastColor = 'text-success';
      }
      this.TOASTS_DIV.innerText = '';
      this.TOASTS_DIV.insertAdjacentHTML(
          'beforeend',
          `
          <div
            class="toast"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            id="toast"
            data-autohide="true"
            data-delay="5000"
          >
            <div class="toast-header ${toastColor}">
              <strong class="mr-auto">${toastHeader}</strong>
              <button
                type="button"
                class="ml-2 mb-1 close"
                data-dismiss="toast"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="toast-body ${toastColor}">
              ${toastContent}
            </div>
          </div>
          `,
      );
      $('#toast').toast('show');
    }

    /**
     * Удалить кнопку скачивания
     * @deprecated Функция отменена
     */
    deleteDownloadBtn() {
      if (this.DOWNLOAD_ACTS_BTN != null) {
        this.DOWNLOAD_ACTS_BTN.remove();
      }
    }

    /**
     * Скачать отчёт и добавить в DOM кнопку скачивания
     * @deprecated Функция отменена
     * @param {Object} res Ответ Fetch API
     */
    pasteDownloadBtn(res) {
      this.MAIN_DATA_DIV.insertAdjacentHTML(
          'beforeend',
          `
          <div class="row justify-content-center">
            <div class="col-auto">
              <button
                class="btn mt-4 btn-primary slideFromBot"
                role="button"
                id="downloadActsBtn"
              >
                <em class="bi bi-file-earmark-word"></em>
                Скачать акты
              </button>
            </div>
          </div>
          `,
      );
      this.DOWNLOAD_ACTS_BTN = document.getElementById('downloadActsBtn');
      this.DOWNLOAD_ACTS_BTN.addEventListener('click', () => {
        multiDownload(FILE_NAMES);
      });
    }

    /**
     * Показать спиннер
     */
    showSpinner() {
      if (this.SPINNER != null) {
        this.SPINNER.classList.remove('d-none');
        this.SPINNER.classList.add('d-inline-block');
        this.GENERATE_REPORT_BTN.classList.remove('btn-darstroy');
        this.GENERATE_REPORT_BTN.classList.add('btn-warning');
        this.GENERATE_REPORT_BTN.firstChild.textContent = 'Акты генерируются';
      }
    }

    /**
     * Скрыть спиннер
     */
    hideSpinner() {
      if (this.SPINNER != null) {
        this.SPINNER.classList.remove('d-inline-block');
        this.SPINNER.classList.add('d-none');
        this.GENERATE_REPORT_BTN.classList.remove('btn-warning');
        this.GENERATE_REPORT_BTN.classList.add('btn-darstroy');
        this.GENERATE_REPORT_BTN.firstChild.textContent = 'Запустить';
      }
    }

    /**
     * Удалить DOM-элемент по ID
     * @param {string} id ID DOM-элемента
     */
    delDOM(id) {
      const DOM_TO_DELETE = document.getElementById(id);
      if (DOM_TO_DELETE != null) {
        DOM_TO_DELETE.remove();
      }
    }

    /**
     * Удалить DOM-элементы по селектору класса
     * @param {string} classname ID DOM-элемента
     */
    delDOMs(classname) {
      const DOM_TO_DELETE = document.querySelectorAll(`.${classname}`);
      if (DOM_TO_DELETE != null) {
        for (const [, EL_OBJ] of Object.entries(DOM_TO_DELETE)) {
          EL_OBJ.remove();
        }
      }
    }
  }

  /**
   * Основной класс работы с приложением
   */
  class Application {
    /**
     * Отправить данные метдом Fetch
     * @param {string} url Скрипт-обработчик
     * @param {Object|string} dataSend Данные для отправки
     * @param {boolean} showSpinner Показать спинер?
     * @return {Object|string} Ответ Fetch
     */
    async sendFetch(url, dataSend = null, showSpinner = false) {
      if (showSpinner) {
        DOM.showSpinner();
      }
      const STRINGIFY_DATA = JSON.stringify({
        IS_CURRENT_USER_ADMIN,
        CURRENT_UID,
        data: dataSend,
      });
      await fetch(url, {
        method: 'POST',
        body: STRINGIFY_DATA,
      })
          .then((response) => {
            if (!response.ok) {
              DOM.generateToast();
            }
            return response.json();
          })
          .then((data) => {
            APP.FETCH_RESPONSE = data;
          });
      if (showSpinner) {
        DOM.hideSpinner();
      }
      return APP.FETCH_RESPONSE;
    }
  }

  /**
   * Инициализация классов
   */
  const DOM = new DOMManipulations();
  const APP = new Application();
});
