<?php
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				if($user_read[3] == 0) header("Location: user.php");
				else if($user_read[3] == 1) header("Location: admin.php");
			}
		}
 	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Регистрация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login">
					<div class="name">Регистрация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Повторите пароль:</div>
					<input name="_passwordCopy" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class="sub-name">Контрольный вопрос (например, имя вашей кошки):</div>
					<input name="_question" type="text" />
					<div class="sub-name">Ответ:</div>
					<input name="_answer" type="text" />
					
					<a href="login.php">Вернуться</a>
					<input type="button" class="button" value="Зайти" onclick="RegIn()" style="margin-top: 0px;"/>
					<img src = "img/loading.gif" class="loading" style="margin-top: 0px;"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			var loading = document.getElementsByClassName("loading")[0];
			var button = document.getElementsByClassName("button")[0];
			
			function RegIn() {
				var _login = document.getElementsByName("_login")[0].value;
				var _password = document.getElementsByName("_password")[0].value;
				var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
				var _question = document.getElementsByName("_question")[0].value;
				var _answer = document.getElementsByName("_answer")[0].value;

				if(_login != "" && _password != "" && _question != "" && _answer != "") {
					if(_password == _passwordCopy) {
						loading.style.display = "block";
						button.className = "button_diactive";

						var data = new FormData();
						data.append("login", _login);
						data.append("password", _password);
						data.append("question", _question);
						data.append("answer", _answer);

						$.ajax({
							url: 'ajax/regin_user.php',
							type: 'POST',
							data: data,
							processData: false,
							contentType: false, 
							success: function (_data) {
								if(_data == "-1") {
									alert("Логин занят.");
								} else if(_data == "-2") {
									alert("Подождите 15 секунд перед следующей попыткой.");
								} else if (!isNaN(_data)) {
									location.href = "index.php";
								} else {
									console.log(_data); 
								}
								loading.style.display = "none";
								button.className = "button";
							}
						});
					} else alert("Пароли не совпадают.");
				} else alert("Заполните все поля, включая контрольный вопрос!");
			}
			
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							if(_passwordCopy != "") {
								RegIn();
							}
						}
					}
				}
			}
			
		</script>
	</body>
</html>