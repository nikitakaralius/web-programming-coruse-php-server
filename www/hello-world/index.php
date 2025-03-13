<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $shoes = isset($_POST['shoes']) ? $_POST['shoes'] : '';
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $animal = isset($_POST['animal']) ? $_POST['animal'] : '';

    $pairs = 0;
    $imagePath = '';

    switch ($shoes) {
        case 'Ботинки':
            $imagePath = 'img/shoes3.png';
            break;
        case 'Сандалии':
            $imagePath = 'img/shoes1.png';
            break;
        case 'Сапоги':
            $imagePath = 'img/shoes2.png';
            break;
    }

    switch ($animal) {
        case 'Паук':
            $pairs = 4;
            break;
        case 'Утка':
            $pairs = 1;
            break;
        case 'Собака':
            $pairs = 2;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Форма</title>
</head>
<body>

<form method="post">
  <label for="name">Имя:</label>
  <input type="text" name="name" id="name" minlength="2">

  <label for="shoes">Обувь:</label>
  <select name="shoes" id="shoes">
    <option value="Ботинки">Ботинки</option>
    <option value="Сапоги">Сапоги</option>
    <option value="Сандалии">Сандалии</option>
  </select>
  <br><br>

  <div style="display: flex">
    <label>
      <input type="radio" name="color" value="Красный"> Красный
    </label>
    <br>
    <label>
      <input type="radio" name="color" value="Белый"> Белый
    </label>
    <br>
    <label>
      <input type="radio" name="color" value="Зеленый"> Зеленый
    </label>
    <br>
    <label>
      <input type="radio" name="color" value="Синий"> Синий
    </label>
    <br><br>
  </div>

  <div style="display: flex; gap: 10px;">
    <input type="submit" name="animal" value="Паук" style="width: 75px">
    <input type="submit" name="animal" value="Утка" style="width: 75px">
    <input type="submit" name="animal" value="Собака" style="width: 75px">
  </div>

</form>

<?php if (!empty($animal)): ?>
<div style="border: 1px solid black; max-width: 400px; margin-top: 10px; display: flex; padding: 10px; gap: 35px">
  <p>
      <?php
      echo "$name, вы выбрали для животного:<br>";
      echo "$animal, $shoes<br>";
      echo "Цвет $color<br>";
      echo "Количество $pairs пары<br>";
      ?>
  </p>
  <p>
    <img src="<?php echo $imagePath; ?>" alt="<?php echo $color; ?>" style="max-width: 75px;">
  </p>
</div>
<?php endif; ?>

</body>
</html>
