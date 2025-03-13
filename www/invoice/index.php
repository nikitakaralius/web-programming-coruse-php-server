<?php
$furniture = [
    "Банкетка",
    "Кровать",
    "Комод",
    "Шкаф",
    "Стул",
    "Стол"
];

$cities = [
    "Москва",
    "Петербург",
    "Нью-Йорк",
    "Берлин",
    "Париж"
];

$colors = [
    "Орех",
    "Дуб мореный",
    "Палисандр",
    "Эбеновое дерево",
    "Клен",
    "Лиственница"
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Заказ мебели</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h1>Оформление заказа</h1>
<form action="invoiceBuilder.php" method="post" enctype="multipart/form-data" class="container v-stack">
  <label for="lastName">Фамилия:</label>
  <input type="text" name="lastName" id="lastName" required>

  <label for="city">Город доставки:</label>
  <select id="city" name="city">
      <?php foreach ($cities as $city): ?>
        <option value="<?= $city ?>"><?= $city ?></option>
      <?php endforeach;?>
  </select>

  <label for="deliveryDate">Дата доставки:</label>
  <input type="date" name="deliveryDate" id="deliveryDate" required>

  <label for="address">Адрес доставки:</label>
  <input type="text" name="address" id="address" required>

  <div class="h-stack">
    <div>
      <label for="color">Выберите цвет мебели:</label>
      <fieldset id="color">
          <?php foreach ($colors as $color): ?>
            <div>
              <input id="<?= $color ?>" type="radio" value="<?= $color ?>" name="color">
              <label for="<?= $color ?>"><?= $color ?></label>
            </div>
          <?php endforeach;?>
      </fieldset>
    </div>

    <div>
      <label>Выберите мебель:</label>
        <?php foreach ($furniture as $value): ?>
          <div class="product">
            <input type="checkbox" name="items[<?= $value ?>]" value="1" id="<?= $value ?>">
            <label for="<?= $value ?>"><?= $value ?></label>
          </div>
        <?php endforeach;?>
    </div>

    <div>
      <label>Выберите количество:</label>
        <?php foreach ($furniture as $value): ?>
          <div class="product">
            <input type="number" name="quantities[<?= $value ?>]" min="1" value="1">
          </div>
        <?php endforeach;?>
    </div>

  </div>

  <label for="priceFile">Загрузите файл price.docx:</label>
  <input type="file" name="priceFile" id="priceFile" accept=".docx" required>

  <input type="submit" name="submit" value="Оформить заказ">
</form>
</body>
</html>
