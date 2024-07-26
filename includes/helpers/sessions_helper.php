<?php

if (!function_exists("session")){
      function session(string $key, array $value=null){
        if (!is_null($value)) {
            $_SESSION[$key] = $value ;
        }
      // $_SESSION[$key];
         return isset( $_SESSION[$key])?$_SESSION[$key]:''  ;
      }
}
if (!function_exists("session_flush")) {
    function session_flush(string $key){
            if(isset($_SESSION[$key])){

              unset($_SESSION[$key]);
            }
    }
}

?>