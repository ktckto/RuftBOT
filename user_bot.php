<?php
require_once ('core.php');
$bot_id=intval($_GET['id']);
if(!empty($bot_id)) {
    $botDB = new BotDB();
    $token = $botDB->getBotTokenById($bot_id);
    $admin_chat_id = $botDB->getAdminChatId($bot_id);
    $user_bot = new Bot($token);
    $input = file_get_contents('php://input');
    $update_json=json_decode($input, TRUE);
    $update = new Update(json_decode($input, TRUE));
    $customer_id = $update->user_id;
    $bot_telegram_id=$user_bot->getMe()['id'];
    //$user_bot->sendMessage(new Message('my id'.$bot_telegram_id),$admin_chat_id);
    //$user_bot->sendMessage(new Message(json_encode(json_decode($input,TRUE))),$admin_chat_id);
    //CASE: the only command (select chat)
    if (!empty($update->text) and $update->text == "/usethischat") {
        //$user_bot->sendMessage(new Message('Changing...'), $update->chat_id);
        $admin_id = $botDB->getAdminId($bot_id);
        if ($update->message['from']['id'] == $admin_id) {
            if ($botDB->setChatID($bot_id, $update->chat_id)) {
                $user_bot->sendMessage(new Message('Chat changed.'), $update->chat_id);
            }
        }
        die();
    }
    if (!empty($update->text) and $update->text == "/start") {
        $welcome_text = $botDB->getWelcomeText($bot_id);
        if (empty($welcome_text)) {
            $welcome_text = 'Created by @ruft_bot';
        }
        $user_bot->sendMessage(new Message($welcome_text), $customer_id);
        $botDB->addUserToBot($customer_id, $bot_id);
        die();
    }
    //CASE: REPLY FROM ADMIN
    if(($update->is_reply) and ($update->chat_id==$admin_chat_id) and ($update->reply_to_message['from']['id']==$bot_telegram_id))
    {
        $send_to_id=$update->reply_to_message['forward_from']['id'];
        if(empty($send_to_id)){
            $send_to_id=$update->reply_to_message['from']['id'];
        }
        //$user_bot->sendMessage(new Message('Відповідь надіслано '.$send_to_id),$admin_chat_id);
        if(!$update->has_media){
            $user_bot->sendMessage(new Message($update->text),$send_to_id);
        }
        else{
            if($update->has_media=='sticker'){
                $user_bot->sendSticker(new Sticker($update->file_id),$send_to_id);
            }
            elseif($update->has_media=='photo'){
                $photo = new Photo($update->file_id);
                if(!empty($update->caption)){
                    $photo->setCaption($update->caption);
                }
                $user_bot->sendPhoto($photo,$send_to_id);
            }
            elseif($update->has_media=='video'){
                $video = new Video($update->file_id);
                if(!empty($update->caption)){
                    $video->setCaption($update->caption);
                }
                $user_bot->sendVideo($video,$send_to_id);
            }

            elseif($update->has_media=='document'){
                $document = new Document($update->file_id);
                if(!empty($update->caption)){
                    $document->setCaption($update->caption);
                }
                $user_bot->sendDocument($document,$send_to_id);
            }
        }
    }
    //CASE: SUBMISSION FROM A USER
    //else it's someone writing
    elseif($update->chat_id>0)
    {
       // $user_bot->sendMessage(new Message('Ваше повідомлення отримано!'),$customer_id);


        {
            //anyway let's forward this shit, ok?
            //error_log(json_encode($update->message));
            if(!empty($update->message['message_id'])){
                $user_bot->forwardMessage(new ForwardedMessage($update->message),$admin_chat_id);
            }
        }
    }
}
