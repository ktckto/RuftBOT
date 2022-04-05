<?php
class Message {
    public $text;
    public $parse_mode='html';
    public string $reply_markup='';
    function __construct($text=''){
        $this->text=$text;
    }
    function addInlineKeyboard(InlineKeyboardMarkUp $keyboard){
        $this->reply_markup=json_encode(['inline_keyboard'=>$keyboard->inline_keyboard]);
    }
}
class ForwardedMessage {
    public int $message_id;
    public int|string $from_chat_id;
    function __construct(array $message){
        if(empty($message['message_id'])){
           $this->message_id=0;
           $this->from_chat_id=0;
        }
        else {
            $this->message_id = $message['message_id'];
            $this->from_chat_id = $message['from']['id'];
        }
    }
}
class Sticker {
    public string $sticker;
    function __construct(string $file_id)
    {
        $this->sticker=$file_id;
    }
}
class Photo {
    public string $photo;
    public string $caption='';
    function __construct(string $file_id){
        $this->photo=$file_id;
    }
    function setCaption(string $caption){
        $this->caption=$caption;
    }
}
class Video {
    public string $video;
    public string $caption='';
    function __construct(string $file_id){
        $this->video=$file_id;
    }
    function setCaption(string $caption){
        $this->caption=$caption;
    }
}
class Document {
    public string $document;
    public string $caption;
    function __construct(string $file_id){
        $this->document=$file_id;
    }
    function setCaption(string $caption){
        $this->caption=$caption;
    }
}