<?php
//depandency
require_once "NewsFinder_V2.php";
require_once "AmUtil.php";
require_once "ImageScrapper.php";
//variables
define("FOSSBYTES_URL", "https://fossbytes.com/");
//const
const FONTES_NOTICIAS_APP =["Fossbytes" ];


const SUPPORTED_URL_FORMATS = [
    ".html", "news", ".aspx", ""
];

function fossbytesUrlTest(){

    $FossBytesNewFinder = new NewsFinder_V2("news" , FOSSBYTES_URL);
    $aAllValidUrlsForTheBoard = $FossBytesNewFinder->allValidUrls();

    $firstPage = $aAllValidUrlsForTheBoard[0];
    $aPairs = $FossBytesNewFinder->allHyperlinksAtBoardUrl($firstPage);

    $results = $FossBytesNewFinder->verifyAnchorFilteringArray($aPairs);

    //filtrar duplicados
    return array_unique($results,SORT_REGULAR);
}//FossBytes



function pesquisaFonteExterna($inputSubMenu){
    $objFonteExterna = new NewsFinder_V2("ExternNews" , $inputSubMenu);
    $arrayNoticias = $objFonteExterna->pesquisarNoticiaFonteExterna($inputSubMenu);;

    $bControllArray = count($arrayNoticias) > 0;

    if($bControllArray){
        echo("Conseguimos tratar o pedido! \n ");
        $inputQuest = readline("Deseja abrir no browser ? \n respostas possiveis (S / Sim | N / Nao ");
        $inputQuest = strtoupper($inputQuest);
        if($inputQuest === "SIM" || $inputQuest === "S" )
            $objFonteExterna->verNoticiasHTML($arrayNoticias);
        else
            var_dump($arrayNoticias);
    }
}//pesquisaFonteExterna

    // -- Create an CMD menu
function menu($input){
    $arrayNoticiasFossbytes = fossbytesUrlTest();

    //escreve a lista de noticias
    switch($input){
            case 1 :
            //var_dump ($arrayNoticiasFossbytes);
            NewsFinder_V2::verNoticiasHTML($arrayNoticiasFossbytes);
            break;
            case 3:
            $inputSubMenu = readline("URL a pesquisar: ");
            pesquisaFonteExterna($inputSubMenu);
            break;
            case 2:
                pesquisarImagensDeNoticiasDoDiaGoogle();
            break;

            case 4:
            $inputSubMenu = readline("URL a verificar: ");
            $cookiesArrayToShow = AmUtil::getCookiesFromWebUrl($inputSubMenu);
            var_dump($cookiesArrayToShow);
            break;
            case 5:
                $inputSubMenu = readline("Novo Path: ");
                NewsFinder_V2::changeBrowserPath($inputSubMenu);
            break;
            case 0:
            exit();
            break;
    }

}//execMenu


//TODO -- Trazer 10 imagens de noticias do dia
function pesquisarImagensDeNoticiasDoDiaGoogle(){

    $imgScrapper = new ImageScrapper();
    $strUrl = $imgScrapper->urlForDay(date("D-M-Y"));

    NewsFinder_V2::criarFicheiro($strUrl);

}//storeValuesDb




function execMenu(){
    echo("
    1 -> Ver noticias atuais Fossbytes \n
    2 -> Pesquisar De Noticias Do Dia Google\n
    3 -> Pesquisar noticias em site externo (requere url) \n
    4 -> Ver cookies de site externo (requere url)\n
    5 -> Modificar caminho do browser (requere PATH) \n
    0 -> sair \n");

    $input = readline("Command: ");

   if ($input !== 5){
       menu($input);
       execMenu();
   }//e

}//execMenu

execMenu();
