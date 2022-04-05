<?php
require_once ('core.php');
$message='Check if bot hosted here, in the USA using form below...';
$h1class='black';
if(!empty($_POST['username'])){
    if(checkCaptcha($config['hcaptcha']['secret'])){
        $botDB=new BotDB();
        $username=trim($_POST['username']);
        $username=str_replace('@','',@$username);
        $bot=$botDB->getBotByUsername($username);
        if($bot){
            $message='Bot is hosted on this server, ruft.org';
            $h1class='green';
        }
        else {
            $message='Bot not found on this server. Probably hosted somewhere else!';
            $h1class='red';
        }
    }
    else {
        $message= 'CAPTCHA invalid.';
        $h1class='red';
    }
}
?>
<!DOCTYPE html>
<head>
    <title> RUFT.ORG bot checker</title>
    <script src='https://www.hCaptcha.com/1/api.js' async defer></script>
    <style>
        .red {
            color:red;
        }
        .green {
            color: green;
        }
    </style>
</head>
<body>
<?php
if(!empty($message)){
    echo '<h1 class="'.$h1class.'">';
    echo $message;
    echo '</h1>';
}
?>

<form method="POST">
    <input name="username" placeholder="@username_bot">
    <div class="h-captcha" data-sitekey="<?=$config['hcaptcha']['public']?>"></div>
    <input type="submit">
</form>
<p>
    Я готов предоставить аудит сервера и выйти на связь при условях:<br>
    1) вы придаете это огласке<br>
    2) вы достаточно понимаете что да как<br>
    3) доступ к серверу я передам при условии что вы являетесь авторитетным лицом и сможете доказать что вы это вы. Либо за вас поручается такое лицо.<br>
    <a href="https://github.com/">Исходный код</a>
</p>
</body>
</html>
