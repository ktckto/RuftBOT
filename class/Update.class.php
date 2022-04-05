<?php
class Update {
    public int $update_id;
    /*
     * Supported types:
     * callback_query
     * message
     */
    public string $update_type='message';
    public array $message=[];
    public int $message_id=0;
    public int $chat_id=0;
    public int $user_id=0;
    public string $callback_query_data='';
    public array $callback_query=[];
    public string $text='';
    public string $caption='';
    public string $username='';
    public bool $is_reply=false;
    public array $reply_to_message=[];
    public bool $is_reply_to_bot=false;
    public bool|string $has_media=false;
    public array $photo=[];
    public array $sticker=[];
    public array $video=[];
    public array $document=[];
    public string $file_id='';
    function __construct(array|null $update){
        if(!empty($update['message'])){
            $this->update_id=$update['update_id'];
            $this->update_type='message';

            $this->message=$update['message'];
            $this->message_id=$this->message['message_id'];
            $this->user_id=$this->message['from']['id'];
            $this->chat_id=$this->message['chat']['id'];
            if(!empty($this->message['from']['username'])){
                $this->username=$this->message['from']['username'];
            }
            if(!empty($this->message['text'])){
                $this->text=$this->message['text'];
            }
            if(!empty($this->message['caption'])){
                $this->caption=$this->message['caption'];
            }
            if(!empty($this->message['sticker'])){
                $this->has_media='sticker';
                $this->sticker=$this->message['sticker'];
                $this->file_id=$this->sticker['file_id'];
            }
            if(!empty($this->message['photo'])){
                $this->has_media='photo';
                $this->photo=end($this->message['photo']);
                $this->file_id=$this->photo['file_id'];

            }
            if(!empty($this->message['video'])){
                $this->has_media='video';
                $this->video=$this->message['video'];
                $this->file_id=$this->video['file_id'];
            }
            if(!empty($this->message['document'])){
                $this->has_media='document';
                $this->document=$this->message['document'];
                $this->file_id=$this->document['file_id'];
            }
            /*if($this->has_media){
                $this->file_id=$this->message['file_id'];
            }*/
            if(!empty($this->message['reply_to_message'])){
                $this->is_reply=true;
                $this->reply_to_message=$this->message['reply_to_message'];
            }
        }
        elseif(!empty($update['callback_query'])){
            $this->update_type='callback_query';
            $this->callback_query=$update['callback_query'];
            $this->message=$this->callback_query['message'];
            $this->callback_query_data=$this->callback_query['data'];
            $this->chat_id=$this->callback_query['chat_instance'];
            $this->message_id=$this->message['message_id'];
            $this->user_id=$this->callback_query['from']['id'];
        }
    }
    function getText(){
        return $this->text;
    }
}