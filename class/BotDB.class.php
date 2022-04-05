<?php
class BotDB
{
    public DB $pdo;

    function __construct()
    {
        $this->pdo = new DB();
    }

    function addUserToBot($user_id,$bot_id){
            $query = "INSERT INTO `bots_users` ( `bot_id`,  `user_id` ) VALUES (  :bot_id,  :user_id )";
            $args = [
                'bot_id' => $bot_id,
                'user_id' => $user_id,
            ];

            try {
                $this->pdo->sql($query, $args);
            } catch (Exception $e) {
                return false;
            }
    }

    function checkUserAccessBot($user_id,$bot_id){
        try {
            $data = $this->pdo->getRow("SELECT * FROM `bots_users` WHERE `bot_id` = ? AND `user_id` = ?", [$bot_id,$user_id]);
            if(!empty($data)){
                return true;
            }
            else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    function getWelcomeText(int $id)
    {
        try {
            $data = $this->pdo->getRow("SELECT welcome FROM `user_bots` WHERE `id` = ?", [$id]);
            if(empty($data)){
                return false;
            }
            return $data['welcome'];
        } catch (Exception $e) {
            return false;
        }
    }
    function getAdminId(int $id)
    {
        try {
            $data = $this->pdo->getRow("SELECT admin_id FROM `user_bots` WHERE `id` = ?", [$id]);
            if(empty($data)){
                return false;
            }
            return $data['admin_id'];
        } catch (Exception $e) {
            return false;
        }
    }
    function getAdminChatId(int $id)
    {
        try {
            $data = $this->pdo->getRow("SELECT chat_id FROM `user_bots` WHERE `id` = ?", [$id]);
            if(empty($data)){
                return false;
            }
            return $data['chat_id'];
        } catch (Exception $e) {
            return false;
        }
    }

    function getBotByToken($token)
    {
        try {
            $data = $this->pdo->getRow("SELECT * FROM `user_bots` WHERE `token` = ?", [$token]);
            return $data;
        } catch (Exception $e) {
            return false;
        }

    }

    function getBotTokenById(int $id)
    {
        try {
            $result= $this->pdo->getRow("SELECT token FROM `user_bots` WHERE id = ?", [$id]);
            if($result){
                return $result['token'];
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    function getBotByUsername(string $username)
    {
        try {
            return $this->pdo->getRow("SELECT * FROM `user_bots` WHERE username = ?", [$username]);
        } catch (Exception $e) {
            return false;
        }
    }
    function getBotsByUserID(int $id){
        try{
            return $this->pdo->getRows("SELECT * FROM `user_bots` WHERE admin_id = ?",[$id]);
        }
        catch (Exception $e){
            return false;
        }
    }
    function updateBotTokenByUsername(string $username, string $token): bool
    {
        $query = "UPDATE `user_bots`  SET `token` = :token  WHERE `username` = :username";
        $args = [
            'username' => $username,
            'token' => $token,
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            var_dump($e);
            return false;
        }
        return true;

    }

    function add_bot(int $admin_id, string $admin_username, string $token)
    {
        $user_bot = new Bot($token);
        $user_bot_info = $user_bot->getMe();

        if ($user_bot_info) {
            $query = "INSERT INTO `user_bots` ( `token`,  `username`,  `admin_id`,  `admin_username`,`chat_id`
  ) VALUES (  :token,  :username,  :admin_id,  :admin_username, :admin_id)";
            $args = [
                'token' => $token,
                'username' => $user_bot_info['username'],
                'admin_id' => $admin_id,
                'admin_username' => $admin_username,
            ];
            try {
                $this->pdo->sql($query, $args);
            } catch (Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }

    }
    function botBelongsTo($user_id,$username){
       $bot= $this->getBotByUsername($username);
       if(!$bot){
           return false;
       }
       if($bot['admin_id']==$user_id){
           return true;
       }
       return false;
    }
    function setMOTD($bot_id,$text){
        $query = "UPDATE `user_bots`  SET `welcome` = :welcome  WHERE `id` = :id";
        $args = [
            'welcome' => $text,
            'id' => $bot_id,
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            error_log($e);
            return false;
        }
    }

    function setChatID($bot_id,$chat_id){
        $query = "UPDATE `user_bots`  SET `chat_id` = :chat_id  WHERE `id` = :id";
        $args = [
            'chat_id' => $chat_id,
            'id' => $bot_id,
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            error_log($e);
            return false;
        }
    }
}