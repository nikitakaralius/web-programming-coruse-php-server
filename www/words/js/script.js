const textInput = document.getElementById('text-input');
const tokensSection = document.getElementById("display-words-section");
const textDisplay = document.getElementById("text-display");
const dragAndDropSection = document.getElementById("drag-and-drop-section");

let tokenList = [];

function onDragStart(event) {
  event.dataTransfer.setData("text", event.target.id);
}

function onDragOver(event) {
  event.preventDefault();
}

function onDisassembleButtonClicked() {
  const text = textInput.value;
  const parsedTokens = parseTokens(text);
  const groupedTokens = groupTokens(parsedTokens);
  tokenList = createMergedTokenList(groupedTokens);
  renderTokenList(tokenList);
}

function parseTokens(text) {
  // HINT: filter(Boolean) to prevent consecutive `-` errors
  return text
    .split('-')
    .map(item => item.trim())
    .filter(Boolean);
}

function groupTokens(tokens) {
  const lowerCaseTokens = [];
  const upperCaseTokens = [];
  const numberTokens = [];

  tokens.forEach(token => {
    if (!isNaN(token)) {
      numberTokens.push(Number(token));
    } else if (token[0] === token[0].toLowerCase()) {
      lowerCaseTokens.push(token);
    } else {
      upperCaseTokens.push(token);
    }
  });

  return {
    lowerCase: lowerCaseTokens.sort(),
    upperCase: upperCaseTokens.sort(),
    numbers: numberTokens.sort((a, b) => a - b)
  };
}

function createMergedTokenList(groupedTokens) {
  const list = [];

  list.push(...createListElements(groupedTokens.lowerCase, 'a'));
  list.push(...createListElements(groupedTokens.upperCase, 'b'));
  list.push(...createListElements(groupedTokens.numbers, 'n'));

  return list;
}

function createListElements(list, type) {
  return list.map((content, index) => ({
    id: `${type}${index + 1}`,
    content: content,
    color: `hsl(${Math.random() * 360}, 70%, 70%)`,
  }))
}

function renderTokenList(items) {
  resetAllSectionsInnerHtml();

  items.forEach(item => {
    const draggableToken = createDraggableToken(item);
    tokensSection.appendChild(draggableToken);
  });
}

function resetAllSectionsInnerHtml() {
  tokensSection.innerHTML = '';
  dragAndDropSection.innerHTML = '';
  textDisplay.innerHTML = '';
}

function createDraggableToken(item) {
  const tokenElement = document.createElement("div");
  tokenElement.id = item.id;
  tokenElement.className = "draggable-item";
  tokenElement.draggable = true;
  tokenElement.textContent = `${item.id} ${item.content}`;
  tokenElement.style.backgroundColor = item.color;
  tokenElement.addEventListener("click", e => appendToOutput(e, item));
  tokenElement.addEventListener("dragstart", onDragStart);

  return tokenElement;
}

function onDrop(event) {
  event.preventDefault();
  const elementId = event.dataTransfer.getData("text");
  const draggedElement = document.getElementById(elementId);

  const targetId = event.target.id;
  const targetParentId = event.target.parentElement.id;

  if (targetId === "drag-and-drop-section" || targetParentId === "drag-and-drop-section") {
    moveToDropArea(draggedElement, event);
  } else if (targetId === "display-words-section" || targetParentId === "display-words-section") {
    returnToTokensSection(draggedElement);
  }
}

function moveToDropArea(element, event) {
  let dropArea = event.target

  if (dropArea.className === "draggable-item") {
    dropArea = dropArea.parentElement;
  }

  element.style.position = "absolute";
  const position = getPosition(event, dropArea, element);
  element.style.left = `${position.x}px`;
  element.style.top = `${position.y}px`;

  element.style.backgroundColor = "lightgrey";
  dropArea.appendChild(element);
}

function returnToTokensSection(element) {
  const returnTokenId = element.textContent.split(" ")[0];
  const returnTokenIndex = tokenList.findIndex(token => token.id === returnTokenId);
  const otherTokensInSection = Array.from(tokensSection.children);

  element.style.position = "static";
  element.style.backgroundColor = tokenList[returnTokenIndex].color;

  for (let i = 0; i < otherTokensInSection.length; i++) {
    const otherTokenId = otherTokensInSection[i].id
    const otherTokenIndex = tokenList.findIndex(token => token.id === otherTokenId);

    if (returnTokenIndex < otherTokenIndex) {
      tokensSection.insertBefore(element, otherTokensInSection[i]);
      return;
    }
  }

  tokensSection.appendChild(element);
}

function getPosition(event, container, element) {
  const containerRect = container.getBoundingClientRect();

  /* HINT: Math.max(0, ...) - x > left boundary && y > top boundary
           Math.min(..., ...): x < right boundary && y < left boundary,
   */
  return {
    x: Math.min(Math.max(0, event.clientX - containerRect.left), containerRect.width - element.offsetWidth),
    y: Math.min(Math.max(0, event.clientY - containerRect.top), containerRect.height - element.offsetHeight),
  };
}

function appendToOutput(event, item) {
  if (event.target.parentElement.id === "drag-and-drop-section") {
    textDisplay.textContent += ` ${item.content}`;
  }
}
