<?php
/*
 * by: Augusto Braun
 * 2020-07-11
 * Dextra PHP Dev Test
 */
@session_start();

require_once('./config.php');

$query = "select * from characters order by charactersName asc";
$characters = $db->consult_array($query);

?><!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="The Vengefuls - API Test for Dextra Admission by: Augusto Braun">
            <meta name="author" content="Augusto Braun">
            <meta name="generator" content="Notepad++">

            <title>The Vengefuls by: Augusto Braun</title>


            <link href="/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="/dist/css/vengefuls.css" rel="stylesheet">

            <script type="text/javascript" src="/dist/js/jquery-3.5.1.min.js"></script>
            <script type="text/javascript" src="/dist/js/bootstrap.min.js"></script>


            <script>

                var characters = {};

                <?php

                    if(!empty($characters) && is_array($characters))
                    {
                        foreach($characters as $char)
                        {
                            echo 'characters["'.$char['charactersId'].'"] = ["'.$char['charactersName'].'","'.$char['charactersExtension'].'","'.$char['charactersDescription'].'","'.$char['charactersDetailUrl'].'","'.$char['charactersComicsUrl'].'"];';
                        }
                    }

                 ?>


                $(document).ready(function()
                {

                    let id;

                    $('#pick_char').change(function()
                    {
                        id = $(this).find("option:selected").attr('value');
                        $('.selected-char').fadeIn();
                        $('.char-card').fadeOut();
                        buildChar(id);

                    });

                    $('.close').click(function()
                    {
                        $('.selected-char').fadeOut();
                    });


                    $('.btn-comics').click(function()
                    {
                        $('.char-card').fadeOut();
                        setTimeout(function(){
                            buildComics(id)
                        },350);
                    });
                });

                function buildChar(id)
                {
                    $('.char-card.character').fadeIn();

                    var extension = characters[id][1];

                    $('.selected-char .image').attr('src','/images/characters/'+ id +'.'+ extension);
                    $('.selected-char .display .name').text(characters[id][0]);
                    $('.selected-char .display .description').text(characters[id][2]);
                    $('.selected-char .display .buttons .btn-know-more').attr('href',characters[id][4]);

                }

                function buildComics(id)
                {
                    var comics = {};
                    var price = '';
                    var buy = '';


                    $('.char-card.comics').fadeIn();

                    params = {
                        id: id,
                        content: 'comics'
                    }

                    $.post('./_build_content.php',params,function(data)
                    {
                        if(data == 'empty')
                        {
                            //create empyt message
                        }else{

                            comics = JSON.parse(data);
                            $.each(comics, function(k,v)
                            {
                                if(v['comicsNumber'] > 0)
                                    price = '<div class="number">'+ v['comicsNumber'] +'</div>';
                                else
                                    price = '';

                                if(v['comicsBuy'] != '')
                                    buy = '<a href="'+ v['comicsBuy'] +'" target="_blank" class="btn btn-success btn-sm buy" role="button" aria-pressed="true">buy</a>';
                                else
                                    buy = '';

                                $('.selected-char .comics .comics-list').append('<div class="col-md-3">' +
                                        price + buy +
                                    '<img class="covers" src="/images/comics/'+ v['comicsId'] + '.'+v['comicsExtension']+'"></div>');
                            })


                        }
                    });
                }

            </script>

        </head>
        <body>

            <div class="containter">

                <div class="title1 avengeance">Your are <span class="yellow">RICK RAGE</span>
                    and your mission is to pick</div><br>
                <div class="title1 avengeance">the most <span class="badaboom title2 red">VENGEFUL</span> heroes to battle<br>
                    against the <span class="badaboom title2 red">OUTSIDERS</span> from the outside</div>
                <?php

                // function that connects to marvel and fetches all the contents and save to local files and database
                // does not activate it unless you want to wait some minutes to populate data.

//                include_once('./_connect_marvel_api.php');


                ?>

                <div class="row mt-5">

                    <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <form>

                            <div class="form-group">

                                <select class="form-control" id="pick_char">
                                    <option>Select You Vengeful...</option>
                                    <?php

                                    if(!empty($characters) && is_array($characters))
                                    {
                                        foreach($characters as $char)
                                        {
                                            echo '<option value="'.$char['charactersId'].'">'.$char['charactersName'].'</option>';
                                        }
                                    }

                                    ?>
                                </select>

                            </div>

                        </form>

                        <div class="selected-char">

                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>

                            <div class="char-card character">
                                <img src="" class="image">
                                <div class="display">
                                    <div class="name red badaboom"></div>
                                    <div class="description comic-book"></div>
                                    <div class="buttons">
                                        <a href="#" class="btn-know-more btn btn-info" role="button" aria-pressed="true" target="_blank">more info</a>
                                        <button type="button" class="btn-comics btn btn-warning">comics</button>
                                        <button type="button" class="btn-events btn btn-warning">events</button>
                                        <button type="button" class="btn-series btn btn-warning">series</button>
                                        <button type="button" class="btn-stories btn btn-warning">stories</button>
                                    </div>
                                    <div class="attribution">Data provided by Marvel. © 2020 MARVEL</div>
                                </div>
                            </div>

                            <div class="char-card comics container">
                                <div class="comics-list row">

                                </div>
                                <div class="attribution">Data provided by Marvel. © 2020 MARVEL</div>
                            </div>

                        </div>

                    </div>
                    <div class="col-md-3"></div>

                </div>

                <div class="copyright">© 2020 MARVEL</div>
            </div>


        </body>
    </html>