<?php

date_default_timezone_set("Europe/Lisbon");
//1º --> definir fontes de noticias
define("NEWS_SOURCE", "https://fossbytes.com/");

//2º --> definir objetos a cortar
define ("CUTTING_MARK", "href=\""); //escape da aspa "
define ("END_CUTTING_MARK", "</a>"); //escape da aspa "

//3º --> fazer o pedido para trazer noticias
$stringNewsCode = file_get_contents(NEWS_SOURCE);
$haveContent = strlen($stringNewsCode) > 0;
// tem resposta?

if($haveContent ){
    /*
      * explode ("*", " a*b*c") --> [" a", "b", "c"]
      */
    $cutedArray = explode(
        CUTTING_MARK,
        $stringNewsCode
    );


    //array para guardar as noticias
    $arrayUrlNews = [] ;
    $numberOfElements = count($cutedArray);
    for( $i = 0+1 ;$i < $numberOfElements; $i++){
        $href = $cutedArray[$i];
        $href = trim($href);

        //valid "http"?
        $validHref = (stripos($href , "https") === 0 );

        $lastIndexOfMark = strpos($href , END_CUTTING_MARK,1);

        $href = substr($href,  0 ,  strlen($href)  - $lastIndexOfMark);


        //controll if already exist
        $isNewHref = array_search($href , $arrayUrlNews) === false ;

        if($isNewHref && strlen($href) === 69)
            $arrayUrlNews[] = $href;


    }


    //escrever noticias
    var_dump($arrayUrlNews);
}

