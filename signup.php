<?php 
$title="Форма регистрации"; // название формы
require __DIR__ . '/header.php'; // шапка проекта
require "db.php"; // подключаем файл для соединения с БД
include "common.php"; // подключаем файл для соединения с БД

// переменная для сбора данных от пользователя по методу POST
$data = $_POST;


// начало выполнения кода
if(isset($data['do_signup'])) {

    //массив для сбора ошибок
	$errors = array();

	// Проводим проверки
        // trim — удаляет пробелы (или другие символы) из начала и конца строки
	
	if(trim($data['name']) == '') {

		$errors[] = "Введите Имя";
	}

	if(trim($data['email']) == '') {

		$errors[] = "Введите Email";
	}

	if(trim($data['number']) == '') {

		$errors[] = "Введите номер телефона! Начинать, с +7 ";

	}

	if($data['password'] == '') {

		$errors[] = "Введите пароль";
	}

	if($data['password_2'] != $data['password']) {

		$errors[] = "Повторный пароль введен не верно!";
	}
         // функция mb_strlen - получает длину строки

    if (mb_strlen($data['name']) < 1 || mb_strlen($data['name']) > 50){
	    
	    $errors[] = "Недопустимая длина имени";

    }

    if (mb_strlen($data['password']) < 5 || mb_strlen($data['password']) > 10){
	
	    $errors[] = "Недопустимая длина пароля (от 1 до 20 символов)";

    }

    // проверка на правильность написания Email
    if (!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $data['email'])) {

	    $errors[] = 'Неверно введен е-mail';
    
    }

	// Проверка на уникальность номера телефона
	if(R::count('users', "number = ?", array($data['number'])) > 0) {

		$errors[] = "Пользователь с таким номером телефона существует!";
	}

	// Проверка на уникальность email

	if(R::count('users', "email = ?", array($data['email'])) > 0) {

		$errors[] = "Пользователь с таким Email существует!";
	}


	if(empty($errors)) {
		// Проверка что таблица существует
		$user = R::dispense('users');

		$user->number = validate_russian_phone_number($data['number']);
		$user->email = $data['email'];
		$user->name = $data['name'];

		// Хешируем пароль
		$user->password = password_hash($data['password'], PASSWORD_DEFAULT);

		R::store($user);
        echo '<div style="color: green; ">Вы успешно зарегистрированы! Можно <a href="login.php">авторизоваться</a>.</div><hr>';

	} else {
		echo '<div style="color: red; ">' . array_shift($errors). '</div><hr>';
	}
}
?>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<div class="container mt-4">
		<div class="row">
			<div class="col">
		<h2>Регистрация</h2>
		<form action="signup.php" method="post">
			<input type="text" class="form-control" name="name" id="name" placeholder=" Введите логин"><br>
			<input type="email" class="form-control" name="email" id="email" placeholder="Введите Email"><br>
			<input type="number" class="form-control" name="number" id="number" placeholder="Введите номер телефона" required><br>
			<input type="password" class="form-control" name="password" id="password" placeholder="Введите пароль"><br>
			<input type="password" class="form-control" name="password_2" id="password_2" placeholder="Повторите пароль"><br>
			<button class="btn btn-success" name="do_signup" type="submit">Зарегистрировать</button>
		</form>
		<br>
		<p>Если вы зарегистрированы, тогда нажмите <a href="login.php">здесь</a>.</p>
		<p>Вернуться на <a href="index.php">главную</a>.</p>
			</div>
		</div>
	</div>
	</body>
</html>