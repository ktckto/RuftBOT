<?php
class User {
    public int $id;
    //public Dialog $dialog;
    public DB $pdo;
    public Dialog $currentDialog;
    function __construct($id)
    {
        $this->id=$id;
        $this->pdo = new DB();
        $this->createDialogRecord();
    }
    public function createDialogRecord(){
        if(!$this->getState()){
            $query = "INSERT INTO `dialogs` (`user_id`, `dialog_id`,  `step`) VALUES (  :user_id,  :dialog_id,:step)";
            $args = [
                'user_id'=>$this->id,
                'dialog_id'=>null,
                'step'=>null
            ];
            try {
                $this->pdo->sql($query, $args);
            }
            catch (Exception $e){
                return false;
            }
        }
    }
    public function getDialogID(){
        $state=$this->getState();
        return $state['dialog_id'];
    }
    public function getStep(){
        $state=$this->getState();
        return $state['step'];
    }
    public function getState(){
            try{
                return $this->pdo->getRow("SELECT * FROM `dialogs` WHERE user_id = ?",[$this->id]);
            }
            catch (Exception $e){
                return false;
            }
    }

    public function setDialog($dialog_id){
        $query = "UPDATE `dialogs`  SET `dialog_id` = :dialog_id, `step` = :step  WHERE `user_id` = :user_id";
        $args = [
            'dialog_id' => $dialog_id,
            'step' => null,
            'user_id'=>$this->id
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function setStep($step_id){
        $query = "UPDATE `dialogs`  SET `step` = :step  WHERE `user_id` = :user_id";
        $args = [
            'step' => $step_id,
            'user_id'=>$this->id
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function setDialogData($data){
        $query = "UPDATE `dialogs`  SET `data` = :data  WHERE `user_id` = :user_id";
        $args = [
            'data' => $data,
            'user_id'=>$this->id
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getDialogData(){
        try {
            $data= $this->pdo->getRow("SELECT data FROM `dialogs` WHERE user_id = ?", [$this->id]);
            if(!empty($data)){
                return $data['data'];
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function clearDialog(){
        $query = "UPDATE `dialogs`  SET `dialog_id` = :dialog_id, `step` = :step, `data` = :data  WHERE `user_id` = :user_id";
        $args = [
            'dialog_id' => null,
            'step' => null,
            'data'=>null,
            'user_id'=>$this->id
        ];
        try {
            $this->pdo->sql($query, $args);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    function newDialog(Dialog $dialog){
        $this->currentDialog=$dialog;
        $this->setDialog($dialog->dialog_id);
        $this->setStep(0);
    }

}
abstract class Dialog {
    public int $dialog_id;
    public int $step_count;
    public array $steps=[];
    public function step($step){
        return $this->steps[$step];
    }
}
class Step {
    public string $type;
    public Message $message;
}
class AddBotDialog extends Dialog{
    function __construct(){
        $this->dialog_id=1;
        $this->step_count=2;
        $step0=new Message('Введите токен бота');
        $step0_keyboard=new InlineKeyboardMarkUp();
        $step0_keyboard->addButton(new InlineKeyboardButton('Отмена','/cleardialog'));
        $step0->addInlineKeyboard($step0_keyboard);
        $this->steps[]=$step0;
    }
}
class MOTDDialog extends Dialog{
    function __construct(){
        $this->dialog_id=2;
        $this->step_count=1;
        $step0=new Message(StaticText::getText('setMOTD'));
        $step0_keyboard=new InlineKeyboardMarkUp();
        $step0_keyboard->addButton(new InlineKeyboardButton('Отмена','/cleardialog'));
        $step0->addInlineKeyboard($step0_keyboard);
        $this->steps[]=$step0;
    }
}