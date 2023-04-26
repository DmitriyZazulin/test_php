<?php
$title="Главная страница"; // название формы
require __DIR__ . '/header.php'; // подключаем шапку проекта
require "db.php"; // подключаем файл для соединения с БД
include "common.php";
?>

<div class="container mt-4">
<div class="row">
<div class="col">
<center>
<h1>Добро пожаловать на наш сайт!</h1>
</center>
</div>
</div>
</div>

<!-- приветствие -->
<?php if(isset($_SESSION['logged_user'])) : ?>
	Привет, <?php echo $_SESSION['logged_user']->name; ?></br>

<?php 
$errords = array();

if (isset($_POST['do_signup'])){
    $user = $_SESSION['logged_user'];

    if(trim($_POST['name']) != '') {
        $user->name = $_POST['name'];
	}
    
    if(trim($_POST['email']) != '') {
        
        if (!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $_POST['email'])) {

	        $errors[] = 'Неверно введен е-mail';
        } 

        if(R::count('users', "email = ?", array($_POST['email'])) > 0) {

            $errors[] = "Пользователь с таким Email существует!";
        }

        $user->email = $_POST['email'];
	}

    if(trim($_POST['number']) != '') {
        if(R::count('users', "number = ?", array($_POST['number'])) > 0) {

            $errors[] = "Пользователь с таким номером телефона существует!";
        }

        $user->number =validate_russian_phone_number($_POST['number']);
	}

    if(empty($errors)) {
        
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        R::store($user);
        $_SESSION['logged_user'] = $user;
        echo '<div style="color: green; "Данные успешно обновлены!</div><hr>';

	} else {
                // array_shift() извлекает первое значение массива array и возвращает его, сокращая размер array на один элемент. 
		echo '<div style="color: red; ">' . array_shift($errors). '</div><hr>';
	}
}
?>

<form action="index.php" method="post">
    <input type="text" class="form-control" name="name" id="name" placeholder=" Введите имя"><br>
    <input type="email" class="form-control" name="email" id="email" placeholder="Введите Email"><br>
    <input type="number" class="form-control" name="number" id="number" placeholder="Введите номер телефона"><br>
    <input type="password" class="form-control" name="password" id="password" placeholder="Введите пароль"><br>
	<button class="btn btn-success" name="do_signup" type="submit">Зарегистрировать</button>
    
</form>

<!-- выйти -->
<a href="logout.php" class="btn btn-danger">Выйти</a> <!-- файл logout.php создадим ниже -->
<?php else : ?>

<!-- Если пользователь не авторизован выведет ссылки на авторизацию и регистрацию -->
<a href="login.php" class="btn btn-info">Авторизоваться</a><br>
<a href="signup.php" class="btn btn-warning">Регистрация</a>
<?php endif; ?>

</body>
</html>