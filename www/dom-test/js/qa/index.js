import {questionsAndAnswers} from "./data.js";
import {shuffle} from "./utils.js";

let selectedQuestionsAndAnswers = [];

const rootElement = document.getElementById('main');
const containerDiv = document.createElement('div');

rootElement.appendChild(createQuestionHeadingElement())
rootElement.appendChild(containerDiv);

let questionToShow = 0;
let correctAnswers = 0;
let areAllQuestionsAnswered = false;

function stepNext() {
  questionToShow++;
  const questionIndex = questionToShow - 1;

  if (questionIndex >= selectedQuestionsAndAnswers.length) {
    areAllQuestionsAnswered = true;
    containerDiv.prepend(createResultBannerElement());
    return;
  }

  containerDiv.appendChild(createQuestionAndAnswerElement(
    questionToShow,
    selectedQuestionsAndAnswers[questionIndex]))
}

function createQuestionHeadingElement() {
  const questionHeadingDiv = document.createElement('div');
  questionHeadingDiv.innerText = 'Вопрос';
  questionHeadingDiv.classList.add('question-heading');

  questionHeadingDiv.addEventListener('click', () => {
    selectedQuestionsAndAnswers = shuffle(questionsAndAnswers);
    questionToShow = 0;
    correctAnswers = 0;
    areAllQuestionsAnswered = false;
    containerDiv.replaceChildren();
    stepNext();
  })

  return questionHeadingDiv;
}

function createQuestionAndAnswerElement(questionNumber, questionAndAnswer) {
  const questionTextSpan = document.createElement('span');
  questionTextSpan.innerText = `${questionNumber}. ${questionAndAnswer.text}`;

  const answerMarkerSpan = document.createElement('span');
  answerMarkerSpan.hidden = true;

  const questionDiv = document.createElement('div');
  questionDiv.appendChild(questionTextSpan);
  questionDiv.appendChild(answerMarkerSpan);
  questionDiv.classList.add('question');

  const answerContainerDiv = document.createElement('div');
  answerContainerDiv.classList.add('answer-container');
  const onAnswerChosen = (wasCorrect) => {
    answerMarkerSpan.hidden = false;
    answerMarkerSpan.innerText = wasCorrect ? '✅' : '❌';
  }

  questionAndAnswer
    .answers
    .map(answer => createAnswerElement(answer, onAnswerChosen))
    .forEach(answerElement => answerContainerDiv.appendChild(answerElement));

  const questionAndAnswerDiv = document.createElement('div');
  questionAndAnswerDiv.appendChild(questionDiv);
  questionAndAnswerDiv.appendChild(answerContainerDiv);

  questionAndAnswerDiv.addEventListener('click', () => {
    if (!areAllQuestionsAnswered) return;

    const explanation = questionAndAnswer.answers.find(x => x.isCorrect).explanation;
    document.getElementById('explanation')?.remove();
    questionAndAnswerDiv.appendChild(createExplanationElement(explanation));
  })

  return questionAndAnswerDiv;
}

function createAnswerElement(answer, onAnswerChosen) {
  const answerDiv = document.createElement('div');
  answerDiv.classList.add('answer');
  answerDiv.classList.add('outlined');
  answerDiv.innerText = answer.text;

  answerDiv.addEventListener('click', () => {
    const allAnswerElements = answerDiv.parentElement.children;

    if (answer.isCorrect) {
      correctAnswers++;

      const explanationElement = createExplanationElement(answer.explanation);
      containerDiv.append(explanationElement);

      for (let i = 0; i < allAnswerElements.length; i++) {
        const answerElement = allAnswerElements[i];
        if (answerElement === answerDiv) {
          answerElement.style.transition = 'transform 3s'
          answerElement.style.transform = `scale(1.1)`;
          continue;
        }

        answerElement.style.zIndex = `1`;
        answerElement.style.transition = 'transform 2s'
        answerElement.style.transform = `translateX(${window.outerWidth}px)`;
      }

      answerDiv.addEventListener('transitionend', () => {
        onAnswerChosen(answer.isCorrect);
        stepNext();
        answerDiv.parentElement.remove();
        explanationElement.remove();
      }, { once: true });
    } else {
      for (let i = 0; i < allAnswerElements.length; i++) {
        const answerElement = allAnswerElements[i];
        answerElement.style.transition = 'transform 2s'
        answerElement.style.transform = `translateX(${window.outerWidth}px)`;
      }

      allAnswerElements[0].addEventListener('transitionend', () => {
        onAnswerChosen(answer.isCorrect);
        stepNext();
        answerDiv.parentElement.remove();
      }, { once: true });
    }
  });

  return answerDiv;
}

function createResultBannerElement() {
  const resultDiv = document.createElement('div');
  resultDiv.classList.add('result-banner');

  resultDiv.innerText =
    `Вопросы закончились. Правильно отвечены ${correctAnswers} из ${selectedQuestionsAndAnswers.length} вопросов`;

  return resultDiv;
}

function createExplanationElement(explanation) {
  const explanationDiv = document.createElement('div');
  explanationDiv.id = 'explanation';
  explanationDiv.innerText = explanation;
  explanationDiv.classList.add('answer-explanation');
  return explanationDiv;
}
