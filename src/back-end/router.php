<?php 
require_once  __DIR__ . '/abstracts/ARouter.php';
require_once __DIR__ . '/user.php';
class router extends ARouter
{

    public function login()
    {
        $this->html("js/login.js","app",["RouterMessage"=>["section"=>"login","class"=>"success","text"=>"Welcome"]]);
        return true;
    }

    public function logout()
    {     
        $this->logData["user"] = user::checkData();
        user::removeData();        
        $this->html("js/login.js","app",["RouterMessage"=>["section"=>"login","class"=>"success","text"=>"Bye Bye"]]);
        return true;
    }

    public function form_authenticate()
    {
        $user_id = ( isset($_POST["user_id"]) ? $_POST["user_id"] : null );
        $pass = ( isset($_POST["pass"]) ? $_POST["pass"] : null );
        $ud = user::getUserData($user_id, $pass);
        if (!is_null($ud)) {
            user::saveData($ud);
            header("Location: index.php");
            return true;
        } else {            
            $this->html("js/login.js","app",["RouterMessage"=>["section"=>"login","class"=>"danger","text"=>"Username or password is wrong"]]);
            return false;
        }
    }

    public function token_authenticate()
    {        
        echo "Not yet supported";
        return false;
    }

    public function send_password_reset_code()
    {
        $user_id = ( isset($_POST["user_id"]) ? $_POST["user_id"] : null );
        if (user::sendPassworResetdCode($user_id)) {            
            $this->html("js/login.js","login",["RouterMessage"=>["section"=>"login","class"=>"success","text"=>"Password reset code has been sent to your email address"]]);
            return ["user_id"=>$user_id,"success"=>true];
        } else {
            $this->html("js/login.js","login",["RouterMessage"=>["section"=>"code","class"=>"danger","text"=>"Password reset code could not be sent"]]);
            return ["user_id"=>$user_id,"success"=>false];
        }        
    }

    public function password_reset_form() {
        $code = ( isset($_GET["code"]) ? trim($_GET["code"]) : "-" );
        $this->html("js/login.js","login",["RouterMessage"=>["section"=>"reset","class"=>"success","text"=>"Please type your new password"],"code"=>$code]);
        return ["code"=>$code];
    }

    public function password_reset()
    {
        $code = ( isset($_POST["code"]) ? $_POST["code"] : null );
        $pass = ( isset($_POST["pass"]) ? $_POST["pass"] : null );

        if (user::resetPassword($code, $pass)) {
            $this->html("js/login.js","app",["RouterMessage"=>["section"=>"login","class"=>"success","text"=>"Password has been reset"]]);
            return ["code"=>$code,"success"=>true];
        } else {
            $this->html("js/login.js","app",["RouterMessage"=>["section"=>"reset","class"=>"danger","text"=>"Password resetting has been failed"],"code"=>$code]);
            return ["code"=>$code,"success"=>false];
        }        
    }

    public function application()
    {
        $user = user::checkData();
        if (!is_null($user)) {            
            $this->html("js/application.js","app",["UserData"=>user::checkData()]);
            return $user;
        } else {            
            $this->login();
            return null;
        }
    }

    public function ajax()
    {
        $data = null;
        $user = user::checkData();
        if (!is_null($user)) {
            require_once __DIR__ . "/ajax.php";
            $ajax = new ajax();
            $data = $ajax->logData;
            $ajax->printAsJsonAndExit();

        } else {
            header('Content-Type: application/json;charset=utf-8;');
            echo json_encode(["success" => false, "text" => "LOGOUT"]);
        }
        return $data;
    }
}
