<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=$this->headers['title']?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="robots" content="all" />
	<meta name ="revisit-after" Content="15 days">
	<meta http-equiv="content-language" content="ru" />
</head>
<body>
<div id="admin-content">
<style>
body {
	background:#fff;
	height:auto;
}
h1 {
	text-align:center;
}

.loginBlock {
    width: 230px;
    margin: 0 auto;
    padding: 20px;
    margin-top: 50px;
    text-align: center;
    border: 1px solid #0808f8;
    border-radius: 3px;
}
form label {
    display: block;
    text-align: left;
    margin-bottom: 0.5em;
}
.input-text {
    width: auto !important;
    padding: 10px 15px !important;
    margin: 20px 0px !important;
    border-radius: 5px !important;
}
.button {
    display: inline-block;
    text-decoration: none;
    font-size: 14px;
    line-height: 23px;
    margin: 0;
    padding: 0 10px 1px;
    cursor: pointer;
    border-width: 1px;
    border-style: solid;
    -webkit-border-radius: 3px;
    -webkit-appearance: none;
    border-radius: 3px;
    white-space: nowrap;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    outline: none;
	padding:5px 30px;
	background-color:#d4dfea;
}
.button:hover{
	background-color:#bcd5ef;
}
</style>
<div class="loginBlock">
<form action="/admin/" method="POST">
	<div>
		<img src="/application/views/gorod24/logo_gorod24.gif" width="100%" alt="полезное радио">
	</div>
	<hr>
	<h3>Авторизация</h3>
	<label for="login">Логин</label>
	<input name="login" id="login" type="text" placeholder="логин" class='text input-text' />
	<label for="password">Пароль</label>
	<input name="password" id="password" type="password" placeholder="пароль"  class='text input-text' />
	<button type="submit" class='button enter-button'>Войти</button>
</form>
</div></div>
</body>
</html>