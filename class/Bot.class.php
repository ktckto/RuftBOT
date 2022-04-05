<?php
class Bot {
    protected $Communicator;
    function __construct($token){
        $this->Communicator=new BotCommunicator($token);
    }
    function editMessageText(Message $message, int $chat_id,int $message_id):false|array{
        return $this->Communicator->send('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>$message->text,
            'parse_mode'=>$message->parse_mode,
            'reply_markup'=>$message->reply_markup
        ]);
    }

    function forwardMessage(ForwardedMessage $forwardedMessage, int $chat_id){
        return $this->Communicator->send('forwardMessage',[
            'chat_id'=>$chat_id,
            'from_chat_id'=>$forwardedMessage->from_chat_id,
            'message_id'=>$forwardedMessage->message_id
        ]);
    }
    function answerCallbackQuery(int $id, string $text='', bool $alert=false):void{
        $this->Communicator->send('answerCallbackQuery',[
            'callback_query_id'=>$id,
            'text'=>$text,
            'show_alert'=>$alert
        ]);
    }

    function sendMessage(Message $message, int $chat_id):bool|array{
        return $this->Communicator->send('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$message->text,
            'parse_mode'=>$message->parse_mode,
            'reply_markup'=>$message->reply_markup
        ]);
    }
    function sendSticker(Sticker $sticker,int $chat_id):array|bool{
        return $this->Communicator->send('sendSticker',[
            'chat_id'=>$chat_id,
            'sticker'=>$sticker->sticker
        ]);
    }
    function sendPhoto(Photo $photo, int $chat_id):array|bool{
        return $this->Communicator->send('sendPhoto',[
            'chat_id'=>$chat_id,
            'photo'=>$photo->photo,
            'caption'=>$photo->caption
        ]);
    }
    function sendVideo(Video $video, int $chat_id):array|bool {
        return $this->Communicator->send('sendVideo',[
            'chat_id'=>$chat_id,
            'video'=>$video->video,
            'caption'=>$video->caption
        ]);
    }
    function sendDocument(Document $document, int $chat_id):array|bool {
        return $this->Communicator->send('sendDocument',[
            'chat_id'=>$chat_id,
            'document'=>$document->document,
            'caption'=>$document->caption
        ]);
    }
    function getMe(){
        return $this->Communicator->send('getMe');
    }
    function getBotId(){
        $data=$this->getMe();
        return $data['id'];
    }
    function setWebHook(string $url):array|bool{
        $this->Communicator->send('deleteWebhook',
        [
            'drop_pending_updates'=>'True'
        ]
        );
        return $this->Communicator->send('setWebhook',
            [
                'url'=>$url,
                //'ip'=>'152.67.76.165'
                //'allowed_updates'=>json_encode(['message','callback_query'])
            ]
        );
    }

}

class BotCommunicator {
    const API_URL='https://api.telegram.org/bot';
    private $endpoint='';
    function __construct($token){
        $this->endpoint=self::API_URL.$token;
    }
    function send(string $method,array $params=[]){
        $ch = curl_init( $this->endpoint.'/'.$method);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->resultToArray($result);
    }
    function resultToArray(string $json):array|bool{
        $array=json_decode($json,true,512);
        if($array['ok']!='true'){
            return false;
        }
        return $array['result'];
    }
}