const calculateResultButton = document.getElementById('calculateResult');
const trenchLengthInput = document.getElementById('trenchLength');
const isBrigadeMechanizedInput = document.getElementById('isBrigadeMechanized');
const resultBlock = document.getElementById('resultBlock');

const onCalculateButtonListener = new Function('onCalculateButtonClicked()');

let trenchCount = 0;

calculateResultButton.addEventListener('click', onCalculateButtonListener);

function onCalculateButtonClicked() {
    const trenchLength = parseFloat(trenchLengthInput.value);
    const isBrigadeMechanized = isBrigadeMechanizedInput.checked;

    if (!trenchLength || trenchLength <= 0) {
        alert('Неверно введена длина канавы');
        return;
    }

    trenchCount = trenchCount >= 3 ? 1 : trenchCount + 1;

    const workersPerMeter = isBrigadeMechanized ? 4 : 3;
    const workersNeeded = Math.ceil(trenchLength / workersPerMeter);

    const shouldShowResult = confirm('Показать результат?');

    const code = `
       Расчет для канавы ${trenchCount}:<br>
       ${shouldShowResult ? getCalculatedResult(trenchLength, workersNeeded, isBrigadeMechanized) : getVacationMessage()} <br>
       `

    if (trenchCount === 1) {
        resultBlock.innerHTML = '';
    }

    resultBlock.innerHTML += code;
}

function getCalculatedResult(trenchLength, workersNeeded, isMechanized) {
    const imagePath = isMechanized ? "img/mechanized-brigade.png" : "img/non-mechanized-brigade.png";

    return `
        Длина канавы: ${trenchLength}<br>
        Тип бригады: ${(isMechanized ? "механизированная" : "не механизированная")}<br>
        Количество землекопов: ${workersNeeded}<br>
        <img src='${imagePath}' alt='Картинка землекопов'>
        `;
}

function getVacationMessage() {
    return `
        <p>Бригада в отпуске</p>
        <img src='img/vacation.png' alt='Картинка в отпуске'>
    `;
}
