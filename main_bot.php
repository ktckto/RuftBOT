<?php
/*
 * Main bot for creating/adding other bots
 * Here one can add token so bot is added to a db
 * or update token
 * Also add chat message to appear (two)
 *
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ('core.php');
$token=$config['main']['token'];
$bot = new Bot($token);

//$bot->sendMessage(new Message(json_encode(json_decode(file_get_contents('php://input'),TRUE))),695054380);
$update = new Update(json_decode(file_get_contents('php://input'), TRUE));
$botDB= new BotDB();
$customer_id=$update->user_id;
$userDB=new User($customer_id);
//callback query handler
if($update->update_type=='callback_query') {
    //
    $bot->answerCallbackQuery($update->callback_query['id'], 'Ok...');
    if (!empty($update->callback_query_data)) {
        $message_id=$update->message_id;
        $data=$update->callback_query_data;
        switch (strtok($update->callback_query_data, ' ')) {
            //commands
            case '/selectchat':
                $message=new Message(StaticText::getText('selectchat'));
                $keyboard=new InlineKeyboardMarkUp();
                $keyboard->addButton(new InlineKeyboardButton('Отмена','/cleardialog'));
                $message->addInlineKeyboard($keyboard);
                $bot->editMessageText($message,$customer_id,$message_id);
                break;
            case '/cleardialog':
                $bot->editMessageText(new Message('Охрана отмєна.'),$customer_id,$message_id);
                $userDB->clearDialog();
                break;
            case '/MOTD':
                $bot_username=trim(mb_substr($data,5));
                if($botDB->botBelongsTo($customer_id,$bot_username)){
                    $userDB->newDialog(new MOTDDialog());
                    $userDB->setDialogData($bot_username);
                    $bot->editMessageText($userDB->currentDialog->step(0),$customer_id,$message_id);
                }
                break;
            case '/edit':
                $bot_username=trim(mb_substr($data,5));
                if($bot_username=='reset') {
                    $user_bots = $botDB->getBotsByUserID($customer_id);
                    if (!$user_bots) {
                        $bot->editMessageText(new Message('У вас нет ботов'), $customer_id, $message_id);
                        break;
                    } else {
                        $message = new Message('Оберіть бота');
                        $keyboard = new InlineKeyboardMarkUp();
                        foreach ($user_bots as $user_bot) {
                            $keyboard->addButton(new InlineKeyboardButton('@' . $user_bot['username'], '/edit ' . $user_bot['username']), 1, 1);
                        }
                        $message->addInlineKeyboard($keyboard);
                        $bot->editMessageText($message, $customer_id, $message_id);
                    }
                }
                if($botDB->botBelongsTo($customer_id,$bot_username)){
                    $message=new Message('Edit @'.$bot_username);
                    $keyboard=new InlineKeyboardMarkUp();
                    $keyboard->addButton(new InlineKeyboardButton('Редагувати вітання','/MOTD '.$bot_username));
                    $keyboard->addButton(new InlineKeyboardButton('Обрати чат для фідбеку','/selectchat'));
                    $keyboard->addButton(new InlineKeyboardButton('Назад','/edit reset'));
                    $message->addInlineKeyboard($keyboard);
                    $bot->editMessageText($message,$customer_id,$message_id);
                }
                break;
        }
    }
}

//command handler
if((!empty($update->text)) and (strpos($update->text,'/')===0)){
    switch (strtok($update->text,' ')){
        //commands
        case '/start':
            $bot->sendMessage(new Message(StaticText::getText('start')),$customer_id);
            break;
        case '/help':
            $bot->sendMessage(new Message(StaticText::getText('help')),$customer_id);
            break;
        case '/changelog':
            $bot->sendMessage(new Message(StaticText::getText('change_notes')),$customer_id);
            break;
        case '/edit':
            $user_bots=$botDB->getBotsByUserID($customer_id);
            if(!$user_bots){
                $bot->sendMessage(new Message('У вас нет ботов'),$customer_id);
                break;
            }
            else {
                $message=new Message('Оберіть бота');
                $keyboard= new InlineKeyboardMarkUp();
                foreach($user_bots as $user_bot){
                    $keyboard->addButton(new InlineKeyboardButton('@'.$user_bot['username'],'/edit '.$user_bot['username']),1,1);
                }
                $message->addInlineKeyboard($keyboard);
                $bot->sendMessage($message,$customer_id);

            }
            break;
        case '/add':
            $userDB->newDialog(new AddBotDialog);
            $bot->sendMessage($userDB->currentDialog->step(0),$customer_id);
            break;
    }
die();
}
if(!empty($update->text)){
    $text=$update->text;
    //dialog handler
    $dialog_id=$userDB->getDialogID();
    switch ($dialog_id){
        case 1:
            //add token
            add_bot_to_db($text);
            $userDB->clearDialog();
            break;
        case 2:
            //MOTD
            $bot_username=$userDB->getDialogData();
            if($botDB->botBelongsTo($customer_id,$bot_username)){
                $bot_id=$botDB->getBotByUsername($bot_username)['id'];
                error_log($bot_id);
                error_log($text);
                $setMOTD=$botDB->setMOTD($bot_id,$text);
                if($setMOTD){
                    $bot->sendMessage(new Message('Успішно.'),$customer_id);
                    $userDB->clearDialog();
                }
                else {
                    $bot->sendMessage(new Message('Помилка. Спробуйте ще раз або скасуйте'),$customer_id);
                }
            }
            break;
    }
}

function add_bot_to_db($user_token){
    global $bot, $customer_id,$botDB,$update;
    $fail_add=false;
    $fail_reason='';
    $user_username='';
    if(!empty($update->username)){
        $user_username=$update->username;
    }
    {
        //retrieve bot
        $user_bot=new Bot($user_token);
        $user_bot_info=$user_bot->getMe();
        if($user_bot_info){
            $bot->sendMessage(new Message("Додаємо бота @".$user_bot_info['username']),$customer_id);
        }
        else {
            $bot->sendMessage(new Message("Невірний токен.".PHP_EOL." /help"),$customer_id);
            return false;
        }

    }
    {
        //add to the DB:
        //check if there is such token in base
        $getBotByToken=$botDB->getBotByToken($user_token);
        if($getBotByToken){
            $bot->sendMessage(new Message("Цей токен вже існує."),$customer_id);
            return false;
        }
        //якщо ми тут, то такого токена не має в БД
        //перевіримо чи маємо ми такого бота за юзернеймом
        $getBotByUsername=$botDB->getBotByUsername($user_bot_info['username']);
        if($getBotByUsername) {
            //ок, маємо. оновимо токен.
            if(!$botDB->updateBotTokenByUsername($user_bot_info['username'],$user_token)){
                $fail_add=true;
                $fail_reason.='Не вдалося оновити токен в базі.'.PHP_EOL;
            }
        }
        else {
            //і тут нема. тоді додамо його.
            if(!$botDB->add_bot($customer_id,$user_username,$user_token)){
                $fail_add=true;
                $fail_reason.='Не вдалося додати бота в базу'.PHP_EOL;
            }

        }
    }
    {
        //set webhook
        if(!$user_bot_id=$botDB->getBotByToken($user_token)['id']){
            $fail_add=true;
            $fail_reason.='Не вдалося встановити вебхук.'.PHP_EOL;
        }
        else {
            $user_bot->setWebHook('https://ruft.org/writebackto/'.$user_bot_id);
        }

    }
    {
        //success or failure
        if($fail_add){
            $reply_finish_add='Не вдалося додати бота! Перевірте токен.'.PHP_EOL;
            $reply_finish_add.=$fail_reason;
            //$reply_finish_add.='Або це щось із нашою системою, таке теж можливо. Сорян.';
        }
        else {
            $reply_finish_add='Додано успішно! Можете користуватися';
            return true;
        }
        $bot->sendMessage(new Message($reply_finish_add),$customer_id);
    }
}
