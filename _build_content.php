<?php

/*
 * by: Augusto Braun
 * 2020-07-11
 * Build the contents of the options: comics, events, stories, series
 */

    @session_start();

    require_once('./config.php');


    if(!$_POST || !$_POST['content'] || !$_POST['id'])
        return false;

    $content = $_POST['content'];
    $id = intVal($_POST['id']);

    $query = "select * from characters_x_".$content." x left join ".$content." c ON x.".$content."Id=c.".$content."Id where x.charactersId=".$id;

    $data = $db->consult_array($query);
    if(!empty($data))
    {
        print_r(json_encode($data, JSON_FORCE_OBJECT));
    }else {
        print_r('empty');
    }



?>