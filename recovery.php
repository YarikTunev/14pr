<?php
    session_start();
    if (isset($_SESSION['user'])) {
        if($_SESSION['user'] != -1) {
            include("./settings/connect_datebase.php");
            
            $user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
            while($user_read = $user_query->fetch_row()) {
                if($user_read[3] == 0) header("Location: user.php");
                else if($user_read[3] == 1) header("Location: admin.php");
            }
        }
    }
?>
<!DOCTYPE HTML>
<html>
    <head> 
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <meta charset="utf-8">
        <title> Восстановление доступа </title>
        <link rel="stylesheet" href="style.css">
        <style>
            .step-2, .success-block { display: none; }
            .mode-selector { margin-bottom: 20px; display: flex; gap: 10px; }
            .mode-btn { cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; background: #f9f9f9; font-size: 13px; }
            .mode-btn.active { background: #e0e0e0; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="top-menu">
            <a href="login.php" class="singin"><img src="img/ic-login.png"/></a>
            <div class="name">
                <a href="index.php">
                    <div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
                    Пермский авиационный техникум им. А. Д. Швецова
                </a>
            </div>
        </div>
        <div class="space"></div>
        <div class="main">
            <div class="content">
                <div class="input-error" style="display: none;">
                    <img src="img/ic-close.png" class="close" onclick="DisableError()"/>
                    <img src="img/ic-error.png"/>
                    Ошибка: <span id="error-text">Данные не верны.</span>
                </div>

                <div class="success-block">
                    <img src="img/ic_success.png">
                    <div class="name">Успешно!</div>
                    <div class="description" id="success-message"></div>
                    <br><a href="login.php" class="button" style="text-decoration:none; display:inline-block; padding: 5px 15px;">На страницу входа</a>
                </div>

                <div class="login">
                    <div class="name">Восстановление доступа</div>
                    
                    <div class="mode-selector">
                        <div id="btn-pass" class="mode-btn active" onclick="SetMode('password')">Восстановить пароль</div>
                        <div id="btn-unlock" class="mode-btn" onclick="SetMode('unlock')">Разблокировать аккаунт</div>
                    </div>

                    <div class="step-1">
                        <div class="sub-name">Ваш логин (E-mail):</div>
                        <input name="_login" type="text" placeholder="E-mail@mail.ru"/>
                        <input type="button" class="button" value="Далее" onclick="GetQuestion()"/>
                    </div>

                    <div class="step-2">
                        <div class="sub-name">Контрольный вопрос:</div>
                        <div id="question-text" style="font-weight: bold; margin-bottom: 10px; color: #333;"></div>
                        
                        <div class="sub-name">Ваш ответ:</div>
                        <input name="_answer" type="text" placeholder="Введите ответ"/>
                        <input type="button" class="button" value="Подтвердить" onclick="VerifyAnswer()"/>
                    </div>

                    <img src="img/loading.gif" class="loading" style="display: none;"/>
                </div>

                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                </div>
            </div>
        </div>

        <script>
            var currentMode = 'password'; // 'password' или 'unlock'
            var errorWindow = document.querySelector(".input-error");
            var loading = document.querySelector(".loading");

            function SetMode(mode) {
                currentMode = mode;
                document.getElementById('btn-pass').className = (mode == 'password' ? 'mode-btn active' : 'mode-btn');
                document.getElementById('btn-unlock').className = (mode == 'unlock' ? 'mode-btn active' : 'mode-btn');
            }

            function DisableError() { errorWindow.style.display = "none"; }
            
            function GetQuestion() {
                var login = document.getElementsByName("_login")[0].value;
                if(!login) return alert("Введите логин");

                loading.style.display = "block";
                
                $.post('ajax/recovery.php', { action: 'get_question', login: login }, function(data) {
                    loading.style.display = "none";
                    if(data == "error_not_found") {
                        document.getElementById('error-text').innerText = "Пользователь не найден.";
                        errorWindow.style.display = "block";
                    } else {
                        document.getElementById('question-text').innerText = data;
                        document.querySelector('.step-1').style.display = "none";
                        document.querySelector('.step-2').style.display = "block";
                        DisableError();
                    }
                });
            }

            function VerifyAnswer() {
                var login = document.getElementsByName("_login")[0].value;
                var answer = document.getElementsByName("_answer")[0].value;
                
                loading.style.display = "block";

                $.post('ajax/recovery.php', { 
                    action: 'verify', 
                    login: login, 
                    answer: answer, 
                    mode: currentMode 
                }, function(data) {
                    loading.style.display = "none";
                    if(data == "success_unlock") {
                        ShowSuccess("Ваш аккаунт успешно разблокирован. Попробуйте войти снова.");
                    } else if(data == "success_password") {
                        ShowSuccess("Пароль сброшен. Проверьте вашу почту (в данной версии пароль обновлен в БД).");
                    } else {
                        document.getElementById('error-text').innerText = "Неверный ответ на контрольный вопрос.";
                        errorWindow.style.display = "block";
                    }
                });
            }

            function ShowSuccess(text) {
                document.querySelector('.login').style.display = "none";
                document.querySelector('.success-block').style.display = "block";
                document.getElementById('success-message').innerText = text;
            }
        </script>
    </body>
</html>