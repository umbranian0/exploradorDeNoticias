<?php
date_default_timezone_set("Europe/Lisbon");

//noticias_do_dia.php

define ("FONTE_NOTICIOSA", "https://observador.pt/");
define ("ONDE_RECORTAR", "href=\""); //escape da aspa "
//define ("ONDE_RECORTAR", 'href="'); //escape da aspa "

//cautela, que em PHP " e ' não são sinónimos
/*
$pi = 3.14;
echo "$pi"; //frases avaliativa => 3.14
echo '$pi'; //frase literal => $pi
*/

$srcCodeComAsNoticias =
    file_get_contents(FONTE_NOTICIOSA);

$bCautelaHouveResposta = strlen($srcCodeComAsNoticias)>0;

if ($bCautelaHouveResposta){
    //havendo resposta, vou recortar da resposta, os URLs
    //para as notícias do dia
    /*
     * explode ("*", " a*b*c") --> [" a", "b", "c"]
     */
    $arrayComAsPartesRecortadas =
        explode(
            ONDE_RECORTAR,
            $srcCodeComAsNoticias
        );

    /*
    $arrayUrlsParaNoticias = array();
    $arrayUrlsParaNoticias = Array();
    */
    $arrayUrlsParaNoticias = [];
    $iQuantosElementos = count($arrayComAsPartesRecortadas);
    for ($idx=0+1; $idx<$iQuantosElementos; $idx++){
        $href = $arrayComAsPartesRecortadas[$idx];
        //endereçamento automágico = arrumação no #1 endereço disponível
        //$arrayUrlsParaNoticias[] = $href; //SEM FILTRO
        $href = trim($href);

        //problemas potenciais na string em $href
        /*
         * 1 só URLs absolutos
         * 2 identificar a aspa
         * 3 só URLs do dia
         */
        //será que href começa por http?
        $bAbsUrl = stripos($href, "http")===0;
        if ($bAbsUrl){
            //castrar onde aparecer a primeira aspa
            $href =
                substr(
                    $href,
                    0,
                    stripos($href, "\"")
                );

            //castração feita, substring só até aspa obtida
            //URL do dia ?
            $filtroDoDia = date("/Y/m/d/");//"2019/10/16/
            $bFiltroAparece =
                stripos(
                    $href,
                    $filtroDoDia
                )!==false;

            $bNovoHref = array_search($href, $arrayUrlsParaNoticias)===false;
            if ($bFiltroAparece && $bNovoHref)
                $arrayUrlsParaNoticias[] = $href;
        }//if
    }//for

    var_dump ($arrayUrlsParaNoticias);
}//
