<?php

// проверка авторизации

if (!isset($_COOKIE['statex_id'])) {
  header('location: ./');
}

// подключаем необходимые модули и загружаем настройки

include('functions.php');
st_load_setting();

unset($content);
unset($form);

if ($_POST['mode'] == 'save') {
    $setting['name'] = $_POST['name'];
    $setting['host'] = $_POST['host'];
    $setting['days'] = $_POST['days'];
    $setting['search'] = $_POST['search'];
    $setting['mail'] = $_POST['mail'];
    $setting['login'] = $_POST['login'];
    $setting['password'] = $_POST['password'];
    $setting['no_variables'] = isset($_POST['no_variables']);
    $setting['use_counter'] = isset($_POST['use_counter']);
    $setting['use_rss'] = isset($_POST['use_rss']);
    $setting['cookie_lifetime'] = $_POST['cookie_lifetime'];
  
    st_save_setting();
}                                              

if ($setting['no_variables']) $form['no_variables'] = 'checked';
if ($setting['use_counter']) $form['use_counter'] = 'checked';
if ($setting['use_rss']) $form['use_rss'] = 'checked';
if ($setting['send_mail']) $form['send_mail'] = 'checked';

include('templates/settings.html');

?>
