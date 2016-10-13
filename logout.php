<?php

setcookie('statex_id', '', (time() - 36000), '/');
setcookie('statex_login', '', (time() - 36000), '/');

unset($_COOKIE);
      
header('location: ./');

?>
