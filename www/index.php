<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0">
  <title>Nikita Karalyus</title>
  <link rel="stylesheet" href="main/css/theme.css">
  <link rel="stylesheet" href="main/css/header.css">
  <link rel="stylesheet" href="main/css/styles.css">
  <script defer src="main/js/main.js"></script>
</head>
<body>
  <header>
    <nav>
      <ul class="nav-list nav-list-mobile">
        <li class="nav-item">
          <div class="mobile-menu">
            <span class="line line-top"></span>
            <span class="line line-bottom"></span>
          </div>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link nav-link-logo">NK</a>
        </li>
        <li class="nav-item">
          <div class="theme-toggle"></div>
        </li>
      </ul>
      <ul class="nav-list nav-list-larger">
        <li class="nav-item nav-item-hidden">
          <a href="#" class="nav-link nav-link-logo">NK</a>
        </li>
        <li class="nav-item">
          <a href="#main" class="nav-link">Home</a>
        </li>
        <li class="nav-item">
          <a href="#my-work" class="nav-link">My Work</a>
        </li>
        <li class="nav-item">
          <a href="#stack" class="nav-link">Stack</a>
        </li>
        <li class="nav-item">
          <a href="#contacts" class="nav-link">Contact Me</a>
        </li>
        <li class="nav-item nav-item-hidden">
          <div class="theme-toggle"></div>
        </li>
      </ul>
    </nav>
  </header>
  <section id="main" class="main animated-appearance">
    <div class="section-content">
      <h1>Верстка и <br/> Прототипирование <br/> Сайтов</h1>
    </div>
  </section>
  <section id="my-work" class="navigation-offset animated-appearance">
    <div class="section-content">
      <h1 class="section-heading">Работы.</h1>
      <div class="carousel animated-appearance">
        <a class="card" href="css-drawing">
          <img src="main/img/my-work/css-drawing.png" alt="css-drawing">
        </a>
        <a class="card" href="svg-animation">
          <img src="main/img/my-work/svg-animation.png" alt="svg-animation">
        </a>
        <a class="card" href="dom-test">
          <img src="main/img/my-work/dom-test.png" alt="dom-test">
        </a>
        <a class="card" href="practical-work">
          <img src="main/img/my-work/practice.png" alt="practice">
        </a>
        <a class="card" href="words">
          <img src="main/img/my-work/words.png" alt="words">
        </a>
      </div>
    </div>
  </section>
  <section id="stack" class="navigation-offset animated-appearance">
    <div class="section-content">
      <h1 class="section-heading">Технологии.</h1>
      <div class="stack-container animated-appearance">
        <div class="stack-item">
          <img src="main/img/stack/html-logo.svg" alt="html-logo" />
          <h2>HTML5</h2>
          <p>HTML5 используется для структурирования и представления содержимого веб-страниц, обеспечивая семантическую разметку и улучшенную поддержку мультимедиа, таких как аудио и видео. Кроме того, HTML5 включает в себя новые API и элементы, которые позволяют создавать более интерактивные и динамичные веб-приложения без необходимости в сторонних плагинах</p>
        </div>
        <div class="stack-item">
          <img src="main/img/stack/css-logo.svg" alt="css-logo" />
          <h2>CSS3</h2>
          <p>CSS3 используется для оформления веб-страниц, позволяя разработчикам задавать стили, такие как цвета, шрифты и расположение элементов, что улучшает визуальное восприятие контента. Кроме того, CSS3 поддерживает анимации и переходы, что позволяет создавать более интерактивные и динамичные пользовательские интерфейсы без необходимости в JavaScript.</p>
        </div>
        <div class="stack-item">
          <img src="main/img/stack/js-logo.svg" alt="js-logo" />
          <h2>JavaScript</h2>
          <p>JavaScript — это язык программирования, который привносит интерактивность и динамичность на веб-страницы, позволяя создавать анимации, управлять пользовательскими действиями и обновлять контент без перезагрузки. Он служит основой для разработки современных веб-приложений, обеспечивая возможность взаимодействия с сервером и манипуляции элементами DOM.</p>
        </div>
        <div class="stack-item">
          <img src="main/img/stack/new-php-logo.svg" alt="php-logo" />
          <p>PHP — это язык программирования, который выполняется на сервере и отвечает за генерацию динамического контента на веб-страницах. Он позволяет обрабатывать данные форм, работать с базами данных, управлять сессиями пользователей и формировать HTML-код перед отправкой в браузер. Будучи одной из ключевых технологий бэкенд-разработки, PHP широко используется для создания сайтов, веб-приложений и API, обеспечивая их функциональность и взаимодействие с серверными ресурсами.</p>
          <a href="hello-world">Перейти к hello-world</a>
        </div>
      </div>
    </div>
  </section>
  <section id="contacts" class="navigation-offset animated-appearance">
    <div class="section-content">
      <h1 class="section-heading">Связаться.</h1>
      <form action="#" method="POST" class="contact-form">
        <div class="form-group">
          <label for="name">Имя:</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="email">Почта:</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="message">Сообщение:</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="submit-button">Отправить</button>
      </form>
    </div>
  </section>
  <footer>
    Built by @nikitakaralius. The source code is available on <a href="https://github.com/nikitakaralius">GitHub</a>.
  </footer>
</body>
</html>
