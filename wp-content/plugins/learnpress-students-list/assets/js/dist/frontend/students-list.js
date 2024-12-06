/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************!*\
  !*** ./assets/js/frontend/students-list.js ***!
  \*********************************************/
/**
 * Load students list
 * @since 4.0.2
 * @version 1.0.0
 */

const loadMoreStudent = 'students-list-btn-load-more',
  listWrap = 'lp-students-list-wrapper',
  lpDataTarget = 'lp-target',
  classLoading = '.lp-loading-no-css';
const url = lpData.lp_rest_url + 'lp/v1/load_content_via_ajax/';

// Events
document.addEventListener('click', e => {
  const target = e.target;
  if (target.classList.contains(loadMoreStudent)) {
    e.preventDefault();
    loadMoreStudentList(target);
  }
});
document.addEventListener('change', e => {
  const target = e.target;
  if (target.id === 'students-list-filter-select') {
    filterStudentList(target);
  }
});
const loadMoreStudentList = btnLoadMore => {
  const lpTargetEle = btnLoadMore.closest(`.${lpDataTarget}`),
    elLoading = btnLoadMore.querySelector(classLoading),
    dataObj = JSON.parse(lpTargetEle.dataset.send),
    dataSend = {
      ...dataObj
    };
  const elUl = lpTargetEle.querySelector(`.${listWrap}`);
  if (!dataSend.args.hasOwnProperty('paged')) {
    dataSend.args.paged = 1;
  }
  dataSend.args.paged++;
  lpTargetEle.dataset.send = JSON.stringify(dataSend);
  if (elLoading) {
    elLoading.classList.remove('hide');
  }
  const callBack = {
    success: response => {
      const {
        status,
        message,
        data
      } = response;
      const {
        total_pages,
        paged
      } = data;
      const newEl = document.createElement('div');
      newEl.innerHTML = data.content;
      const itemsNew = newEl.querySelectorAll('.lp-student-enrolled');
      if (itemsNew.length) {
        itemsNew.forEach(item => {
          elUl.insertAdjacentElement('beforeend', item);
        });
      }
      if (paged >= total_pages) {
        btnLoadMore.remove();
      }
    },
    error: error => {
      console.log(error);
    },
    completed: () => {
      if (elLoading) {
        elLoading.classList.add('hide');
      }
      btnLoadMore.classList.remove('disabled');
    }
  };
  window.lpAJAXG.fetchAPI(url, dataSend, callBack);
};
const filterStudentList = elFilter => {
  const lpTargetEle = elFilter.closest(`.${lpDataTarget}`),
    dataObj = JSON.parse(lpTargetEle.dataset.send),
    dataSend = {
      ...dataObj
    };
  const elLoadingChange = lpTargetEle.closest('.lp-load-ajax-element').querySelector('.lp-loading-change');
  if (elLoadingChange) {
    elLoadingChange.style.display = 'block';
  }
  dataSend.args.paged = 1;
  dataSend.args.status = elFilter.value;
  lpTargetEle.dataset.send = JSON.stringify(dataSend);
  const callBack = {
    success: response => {
      const {
        status,
        message,
        data
      } = response;
      lpTargetEle.innerHTML = data.content;
    },
    error: error => {
      console.log(error);
    },
    completed: () => {
      if (elLoadingChange) {
        elLoadingChange.style.display = 'none';
      }
    }
  };
  window.lpAJAXG.fetchAPI(url, dataSend, callBack);
};
/******/ })()
;
//# sourceMappingURL=students-list.js.map