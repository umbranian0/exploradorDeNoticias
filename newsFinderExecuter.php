<?php
//wg2.php
require_once "NewsFinder_V2.php";
define("FOSSBYTES_URL", "https://fossbytes.com/");
const FONTES_NOTICIAS_APP =["Fossbytes" ];
function fossbytesUrlTest(){

    $FossBytesNewFinder = new NewsFinder_V2("news" , FOSSBYTES_URL);
    $aAllValidUrlsForTheBoard = $FossBytesNewFinder->allValidUrls();

    $firstPage = $aAllValidUrlsForTheBoard[0];
    $aPairs = $FossBytesNewFinder->allHyperlinksAtBoardUrl($firstPage);

    $results = fossBytesFilteringArray($aPairs) ;
    echo("A filtrar valores!");
    //TODO -- Store values on DB
    storeValuesDb($results);
    echo("A retornar valores");
    return $results;
}//FossBytes

function fossBytesFilteringArray(array $pArrayToFilter){
    $filtredArr = [];
    foreach($pArrayToFilter as $item){
        if(strlen($item["anchor"]) >= 40)
            $filtredArr[] = $item;
    }
    return $filtredArr;
}

    //TODO -- Create an CMD menu
function execMenu(){
    echo("1 -> Ver noticias atuais ". FONTES_NOTICIAS_APP ." /n
    2 -> Pesquisar noticia por dia /n
    3 -> Ver noticias HTML
    4 -> sair");

    $input = readline("Command: ");
    //escreve a lista de noticias
    switch($input){
        case 1 :
            var_dump (fossbytesUrlTest());
            break;
        case 2:
            $inputSubMenu = readline("Data a pesquisar: ");
            pesquisarNoticiasDB_porDia($inputSubMenu);
            break;
        case 3:
            verNoticiasHTML();
            break;
        case 4:
            exit();
            break;
    }

}//execMenu
//TODO -- Create an CMD menu
function storeValuesDb(array $pArrayToStore){
    //ter em atenção duplicados
}
//TODO --Criar uma pesquisa na DB
function pesquisarNoticiasDB_porDia(string $pSubMenuInput){

}
//TODO --Criar e abrir um HTML no browser
function verNoticiasHTML(){

}


execMenu();
