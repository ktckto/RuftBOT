<?php
class StaticText{
    static function getText($id) {
        return file_get_contents('texts/'.$id.'.html');
    }
}