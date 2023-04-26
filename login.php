<?php 
$title="Форма авторизации"; // название формы
require __DIR__ . '/header.php'; // шапка проекта
require "db.php"; // файл для соединения с БД
include "common.php";
// переменная для сбора данных от пользователя по методу POST
$data = $_POST;

// начало выполнения кода 
if(isset($data['do_login'])) { 

 // Масив для ошибок 
 $errors = array();

// секретны ключ 
// я не боюсь показать ключ, поскольку он распространен только на локалхост
$secret = '6LcHmrclAAAAALc2EROhEBSv2IN0IntfCOUe-zM9';
// однократное включение файла autoload.php (клиентская библиотека reCAPTCHA PHP)
require_once (dirname(__FILE__).'/vendor/autoload.php');
// если в массиве $_POST существует ключ g-recaptcha-response, то...
if (isset($_POST['g-recaptcha-response'])) {
  // создать экземпляр службы recaptcha, используя секретный ключ
  $recaptcha = new \ReCaptcha\ReCaptcha($secret);
  // получить результат проверки кода recaptcha
  $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
  // если результат положительный, то...
  if ($resp->isSuccess()){
    // действия, если код captcha прошёл проверку
    
	
 // поиск пользователей в таблице users
 $user = R::findOne('users', 'number = ? OR email = ?', array(validate_russian_phone_number($data['login']), $data['login']));

 if($user) {

 	// Если логин существует, тогда проверяем пароль
 	if(password_verify($data['password'], $user->password)) {

 		// Все верно, пускаем пользователя
 		$_SESSION['logged_user'] = $user;
 		
 		// Редирект на главную страницу
                header('Location: index.php');

 	} else {
    
    $errors[] = 'Пароль неверно введен!';

 	}

    } else {
 	$errors[] = 'Пользователь с таким логином не найден!';
 }

  } else {
    // иначе передать ошибку

	$errors[] = 'Неверно ввели Капчу';
}

} else {
  //ошибка, не существует ассоциативный массив $_POST["send-message"]
  $data['result']='error';
}

if(!empty($errors)) {

		echo '<div style="color: red; ">' . array_shift($errors). '</div><hr>';

	}
}

?>

<div class="container mt-4">
		<div class="row">
			<div class="col">
		<!-- Форма авторизации -->
		<h2>Форма авторизации</h2>
		<form action="login.php" method="post">
			<input type="text" class="form-control" name="login" id="login" placeholder="Введите почту или номер телефона" required><br>
			<input type="password" class="form-control" name="password" id="pass" placeholder="Введите пароль" required><br>
			
			<!-- добавление элемента div -->
			<div class="g-recaptcha" data-sitekey="6LcHmrclAAAAAErYjQth7_hyqPHf-3vZXcRR5Qg4"></div>
            <!-- элемент для вывода ошибок -->
            <div class="text-danger" id="recaptchaError"></div>
            <!-- js-скрипт гугл капчи -->
            <script src='https://www.google.com/recaptcha/api.js'></script>

			<button class="btn btn-success" name="do_login" type="submit">Авторизоваться</button>
		</form>
		<br>
		<p>Если вы еще не зарегистрированы, тогда нажмите <a href="signup.php">здесь</a>.</p>
		<p>Вернуться на <a href="index.php">главную</a>.</p>
			</div>
		</div>
	</div>

</body>
</html>