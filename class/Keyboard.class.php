<?php
class InlineKeyboardButton {
    public string $text;
    public string $url;
    public string $callback_data;
    function __construct($text,$callback_data){
        $this->text=$text;
        $this->callback_data=$callback_data;
    }
}
class InlineKeyboardMarkUp{
    public array $inline_keyboard;
    function addButton(InlineKeyboardButton $button,int $x=null,int $y=null){
        $this->inline_keyboard[][]=$button;
    }
}