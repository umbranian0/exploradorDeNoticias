<?php
date_default_timezone_set("Europe/Lisbon");
//1º --> definir fontes de noticias
define("NEWS_SOURCE", "https://fossbytes.com/");

//2º --> definir objetos a cortar
define ("CUTTING_MARK", "href=\""); //escape da aspa "

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
        $validHref = stripos($href , "http") === 0;

        //controll if already exists
        $isNewHref = array_search($href , $arrayUrlNews) === false ;
        if($isNewHref)
            $arrayUrlNews[] = $href;
    }
    //escrever noticias
    var_dump($arrayUrlNews);
}

