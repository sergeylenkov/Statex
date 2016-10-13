<?php

include('functions.php');
st_load_setting();

if (isset($_POST['logon'])) {
    if ($_POST['password'] == $setting['password']) {
        if (isset($_POST['remember'])) {
            $lifetime = $setting['cookie_lifetime'] * (60*60*24);
            $lifetime = time() + $lifetime;
        } else {
            $lifetime = 0;
        }

        setcookie('statex_id', rand(0, 1000), $lifetime, '/');    
        setcookie('statex_login', $login, $lifetime, '/');
    }
  
    header('location: ./');
} else {                                  
    include('templates/logon.html');
}

?>
