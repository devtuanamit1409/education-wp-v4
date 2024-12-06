/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************!*\
  !*** ./assets/src/js/admin-random-quiz.js ***!
  \********************************************/
/**
 * Random quiz script
 *
 * @since 4.0.2
 * @version 1.0.0
 */

let elAdminEditorQuiz;
const getTotalQuestions = () => {
  elAdminEditorQuiz = document.querySelector('#admin-editor-lp_quiz');
  if (!elAdminEditorQuiz) {
    return;
  }
  const listQuestions = elAdminEditorQuiz.querySelectorAll('.question-item');
  const totalQuestions = listQuestions.length;
  const elTotalQuestion = document.querySelector('.lp-quiz-random-questions-meta-box-fields span.total_questions');
  const eleRandomQuestion = document.querySelector('input#number_questions_random');
  if (elTotalQuestion !== null) {
    elTotalQuestion.innerText = totalQuestions;
  }
  if (eleRandomQuestion !== null) {
    eleRandomQuestion.setAttribute('max', totalQuestions);
    if (eleRandomQuestion.value > totalQuestions) {
      eleRandomQuestion.value = totalQuestions;
    }
  }
  return totalQuestions;
};

/**
 * Events
 */
document.addEventListener('change', function (e) {
  const self = e.target;
  elAdminEditorQuiz = document.querySelector('#admin-editor-lp_quiz');
  if (elAdminEditorQuiz) {
    if (self.closest('#admin-editor-lp_quiz')) {
      getTotalQuestions();
    }
  }
});
document.addEventListener('click', function (e) {
  const selft = e.target;
  const classList = selft.classList;
  if (selft.closest('#admin-editor-lp_quiz')) {
    const totalQuestionsOld = getTotalQuestions();
    const interVal = setInterval(function () {
      const totalQuestionsNew = getTotalQuestions();
      if (totalQuestionsOld !== totalQuestionsNew) {
        clearInterval(interVal);
      }
    }, 100);
  }
});
/******/ })()
;
//# sourceMappingURL=admin-random-quiz.js.map