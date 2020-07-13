<?php

/*
 * by: Augusto Braun
 * 2020-07-11
 * API Connection to Marvel's database to suck lots of contents to local database
 */

    $marvel_usr = 'marvel348580899';
    $public_key = '3e34603d82ff4d468843e222ddb951b2';
    define('APIKEY',$public_key);
    $private_key = 'c953843556e2749abde3a815be08cbac855d610e';
    $endpoint = 'http://gateway.marvel.com/v1/public/characters';
    define('ENDPOINT',$endpoint);
    $time = time();
    $hash = md5($time.$private_key.$public_key);
    $query_params = ['apikey' => $public_key, 'ts' => $time, 'hash' => $hash];
    $query = http_build_query($query_params);
    define('QUERY',$query);

    //defines the limit of characters to populate database
    $limit = 50;

    $vengefuls = array();
    $vengefuls = json_decode(file_get_contents($endpoint.'?limit='.$limit.'&'.$query), true);


    /*
     * THE ENDPOINTS /v1/public/characters
     * AND /v1/public/characters/{characterId}
     * BRINGS THE SAME RESULTS FOR EACH CHARACTER, SO I'LL SKIP THE SECOND
     */

    echo '<pre>';

    if(!empty($vengefuls))
    {
        foreach($vengefuls['data']['results'] as $k=>$v)
        {
            foreach($v['urls'] as $k2=>$v2)
            {
                if($v2['type'] == 'detail')
                    $detail = $v2['url'];
                if($v2['type'] == 'comiclink')
                    $comiclink = $v2['url'];
            }

            $query = "insert into characters (charactersId,charactersName,charactersExtension,charactersDescription,charactersDetailUrl,charactersComicsUrl)
                      values ('".$v['id']."','".addSlashes($v['name'])."','".$v['thumbnail']['extension']."','".addSlashes($v['description'])."','".$detail."','".$comiclink."')
                      on duplicate key update charactersId=LAST_INSERT_ID(charactersId)";

            if($charId = $db->consult_id($query))
            {
                print_r($query.'<hr>'); //logs on screen

                //saving a copy for offline tests
                saveThumb($v['thumbnail']['path'],$v['thumbnail']['extension'],$v['id'],'characters');
                usleep(1000);

                //saving all endpoints for this character, except for 'character' that doesn't populate any new data
                saveStuff($v['id']);
            }
        }
    }



    function saveStuff($charId=null)
    {
        if(!$charId)
            return false;

        $db = new Model();
        $stuff = array('comics','events','series','stories');

        foreach($stuff as $k=>$v)
        {
            $endpoint = ENDPOINT.'/'.$charId.'/'.$v.'?'.QUERY;
            $data = json_decode(file_get_contents($endpoint), true);
            if(isset($data['data']['results']) && !empty($data['data']['results']))
            {
                foreach($data['data']['results'] as $k2=>$v2)
                {
                    if($v == 'comics')
                    {
                        set_time_limit(1000);
                        $query = "select comicsId from comics where comicsId=".$v2['id'];
                        $id = $db->consult_array($query,'comicId');
                        if(empty($id))
                        {
                            $comicBuy = (!empty($v2['urls'][1]['url'])) ? $v2['urls'][1]['url'] : '';
                            $comicPrice = (!empty($v2['prices'][0]['price'])) ? $v2['prices'][0]['price'] : '';
                            $comicExtension = (!empty($v2['thumbnail']['extension'])) ? $v2['thumbnail']['extension'] : '';
                            $query = "insert into comics (comicsId,comicsName,comicsExtension,comicsNumber,comicsPrice,comicsBuy,comicsDescription,comicsUrl)
                              values ('".$v2['id']."','".addSlashes($v2['title'])."','".$comicExtension."','".$v2['issueNumber']."','".$comicPrice."','".$comicBuy."','".addSlashes($v2['description'])."','".$v2['resourceURI']."')
                            on duplicate key update comicsId=LAST_INSERT_ID(comicsId)";
                            print_r($query.'<hr>');
                            $db->consult($query);
                            $id = $v2['id'];
                            usleep(1000);
                        }else{
                            $id = $id[0];
                        }
                        $query = "insert ignore into characters_x_comics (charactersId,comicsId) values ('".$charId."','".$id."')";
                        print_r($query.'<hr>');
                        $db->consult($query);
                        usleep(1000);

                    }elseif($v == 'events')
                    {
                        set_time_limit(1000);
                        $query = "select eventsId from events where eventsId=".$v2['id'];
                        $id = $db->consult_array($query,'eventId');
                        if(empty($id))
                        {
                            $url = (!empty($v2['urls'])) ? $v2['urls'][0]['url'] : '';
                            $eventExtension = (!empty($v2['thumbnail']['extension'])) ? $v2['thumbnail']['extension'] : '';
                            $query = "insert into events (eventsId,eventsTitle,eventsExtension,eventsDescription,eventsUrl,eventsDetail)
                              values ('".$v2['id']."','".addSlashes($v2['title'])."','".$eventExtension."','".addSlashes($v2['description'])."','".$v2['resourceURI']."','".$url."')
                            on duplicate key update eventsId=LAST_INSERT_ID(eventsId)";
                            print_r($query.'<hr>');
                            $db->consult($query);
                            $id = $v2['id'];
                            usleep(1000);
                        }else{
                            $id = $id[0];
                        }
                        $query = "insert ignore into characters_x_events (charactersId,eventsId) values ('".$charId."','".$id."')";
                        print_r($query.'<hr>');
                        $db->consult($query);
                        usleep(1000);

                    }elseif($v == 'series')
                    {
                        set_time_limit(1000);
                        $query = "select seriesId from series where seriesId=".$v2['id'];
                        $id = $db->consult_array($query,'seriesId');
                        if(empty($id))
                        {
                            $url = (!empty($v2['urls'][0]['url'])) ? $v2['urls'][0]['url'] : '';
                            $seriesExtension = (!empty($v2['thumbnail']['extension'])) ? $v2['thumbnail']['extension'] : '';
                            $query = "insert into series (seriesId,seriesTitle,seriesExtension,seriesDescription,seriesDetail,seriesRating,seriesStart,seriesEnd,seriesComics)
                              values ('".$v2['id']."','".addSlashes($v2['title'])."','".$seriesExtension."','".addSlashes($v2['description'])."','".$url."','".$v2['rating']."','".$v2['startYear']."','".$v2['endYear']."','".$v2['comics']['available']."')
                            on duplicate key update  seriesId=LAST_INSERT_ID(seriesId)";
                            print_r($query.'<hr>');
                            $db->consult($query);
                            $id = $v2['id'];
                            usleep(1000);
                        }else{
                            $id = $id[0];
                        }
                        $query = "insert ignore into characters_x_series (charactersId,seriesId) values ('".$charId."','".$id."')";
                        print_r($query.'<hr>');
                        $db->consult($query);
                        usleep(1000);

                    }elseif($v == 'stories')
                    {
                        set_time_limit(1000);
                        $storiesExtension = (!empty($v2['thumbnail']['extension'])) ? $v2['thumbnail']['extension'] : '';
                        $query = "select storiesId from stories where storiesId=" . $v2['id'];
                        $id = $db->consult_array($query, 'storiesId');
                        if (empty($id))
                        {
                            $issueName = (!empty($v2['originalIssue']['name'])) ? $v2['originalIssue']['name'] : '';
                            $issueUrl = (!empty($v2['originalIssue']['resourceURI'])) ? $v2['originalIssue']['resourceURI'] : '';
                            $query = "insert into stories (storiesId,storiesTitle,storiesExtension,storiesDescription,storiesType,storiesOriginalIssue,storiesOriginalIssueUrl)
                              values ('" . $v2['id'] . "','" . addSlashes($v2['title']) . "','".$storiesExtension."','" . addSlashes($v2['description']) . "','" . $v2['type'] . "','".$issueName."','".$issueUrl."')
                            on duplicate key update storiesId=LAST_INSERT_ID(storiesId)";
                            print_r($query.'<hr>');
                            $db->consult($query);
                            $id = $v2['id'];
                            usleep(1000);
                        } else {
                            $id = $id[0];
                        }
                        $query = "insert ignore into characters_x_stories (charactersId,storiesId) values ('" . $charId . "','" . $id . "')";
                        print_r($query.'<hr>');
                        $db->consult($query);
                        usleep(1000);
                    }

                    if(!empty($v2['thumbnail']['path']) && !empty($v2['thumbnail']['extension']))
                        saveThumb($v2['thumbnail']['path'],$v2['thumbnail']['extension'],$v2['id'],$v);
                }
            }
        }
        return true;
    }


    function saveThumb($path=null,$extension=null,$itemId=null,$folder=null)
    {
        set_time_limit(5000);
        if(!$path || !$extension || !$itemId || !$folder)
            return false;

        if(is_file('./images/'.$folder.'/'.$itemId.'.'.$extension))
            return false;

        if (!is_dir('./images/'.$folder))
            mkdir('./images/'.$folder, 0755, true);

        set_time_limit(1000);
        $thumb = file_get_contents($path.'.'.$extension);
        $local = './images/'.$folder.'/'.$itemId.'.'.$extension;
        $file_handler = fopen($local,'w');
        fwrite($file_handler,$thumb);
        fclose($file_handler);
        print_r('Imagem: '.$local.'<hr>');
        return true;
    }


?>