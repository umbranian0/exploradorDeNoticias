<?php
//depandency
require_once "NewsFinder_V2.php";
//variables
define("FOSSBYTES_URL", "https://fossbytes.com/");
//const
const FONTES_NOTICIAS_APP =["Fossbytes" ];
const MOZZILA_PATH = "C:/Program Files/Mozilla Firefox/firefox.exe";
const SUPPORTED_URL_FORMATS = [
    ".html", "news", ".aspx", ""
];

function fossbytesUrlTest(){

    $FossBytesNewFinder = new NewsFinder_V2("news" , FOSSBYTES_URL);
    $aAllValidUrlsForTheBoard = $FossBytesNewFinder->allValidUrls();

    $firstPage = $aAllValidUrlsForTheBoard[0];
    $aPairs = $FossBytesNewFinder->allHyperlinksAtBoardUrl($firstPage);

    $results = verifyAnchorFilteringArray($aPairs) ;
    //TODO -- Store values on DB
    storeValuesDb($results);

    //filtrar duplicados
    return array_unique($results,SORT_REGULAR);
}//FossBytes

function verifyAnchorFilteringArray(array $pArrayToFilter){
    $filtredArr = [];
    foreach($pArrayToFilter as $item){
        if(strlen($item["anchor"]) >= 40  ) {
            $filtredArr[] = $item;
        }
    }
    return $filtredArr;
}//fossBytesFilteringArray

    // -- Create an CMD menu
function menu($input){
    $arrayNoticiasFossbytes = fossbytesUrlTest();

    //escreve a lista de noticias
    switch($input){
        case 1 :
            var_dump ($arrayNoticiasFossbytes);
            break;
        case 4:
            $inputSubMenu = readline("Data a pesquisar: ");
            pesquisarNoticiasDB_porDia($inputSubMenu);
            break;
        case 3:
            verNoticiasHTML($arrayNoticiasFossbytes);
            break;
        case 2:
            $inputSubMenu = readline("URL a pesquisar: ");
            $arrayNoticias = pesquisarNoticiaFonteExterna($inputSubMenu);
            $bControllArray = count($arrayNoticias) > 0;

            if($bControllArray){
                echo("Conseguimos tratar o pedido! \n ");
                $inputQuest = readline("Deseja abrir no browser ? \n respostas possiveis (S / Sim | N / Nao ");
                $inputQuest = strtoupper($inputQuest);
                if($inputQuest === "SIM" || $inputQuest === "S" || $inputQuest === "NAO" || $inputQuest === "N"  )
                    verNoticiasHTML($arrayNoticias);
            }
            break;
        case 0:
            exit();
            break;
    }

}//execMenu

function pesquisarNoticiaFonteExterna(string $pUrlFonteExterna){

    if (strpos($pUrlFonteExterna, 'http') === 0) {
        // It starts with 'http'

        $bHrefEndsInSupportedFormat = AmUtil::stringEndsInOneOfThese(
            AmUtil::HREF,
            SUPPORTED_URL_FORMATS
        );
        if($bHrefEndsInSupportedFormat){
            $NewsFinder = new NewsFinder_V2("news" , $pUrlFonteExterna);
            $aAllValidUrlsForTheBoard = $NewsFinder->allValidUrls();

            $firstPage = $aAllValidUrlsForTheBoard[0];
            $aPairs = $NewsFinder->allHyperlinksAtBoardUrl($firstPage);

            $results = verifyAnchorFilteringArray($aPairs) ;

            //filtrar duplicados
            return array_unique($results,SORT_REGULAR);
        }
    }
    else{

        echo("unsupported URL!!");
    }

}//pesquisarNoticiasFonteExterna

//TODO -- Store Values DB
function storeValuesDb(array $pArrayToStore){
    //ter em atenção duplicados
}//storeValuesDb

//TODO --Criar uma pesquisa na DB
function pesquisarNoticiasDB_porDia(string $pSubMenuInput){
    echo("todo");
}//pesquisarNoticiasDB_porDia

// --Criar e abrir um HTML no browser
function verNoticiasHTML(array $pArrayNoticias){
    $myFileName = NewsFinder_V2::linksToHtml_retCaminhoFicheiro($pArrayNoticias);

    $myfile = fopen($myFileName, "r");
    //echo ("a tentar abrir ficheiro");
    if( !file_exists($myFileName ))
       echo ("Unable to open file!");
    //echo fread($myfile,filesize($myFileName));
    fclose($myfile);

    //echo("a abrir HTML");
    $browser_PATH = MOZZILA_PATH;
    //abre o ficheiro com o browser
    system(
        "\"$browser_PATH\" \"$myFileName\"",
        $allOutput
        );

}//verNoticiasHTML

function execMenu(){
    echo("
    1 -> Ver noticias atuais Fossbytes \n
    2 -> Pesquisar noticias em site externo (requere url) \n
    3 -> Ver noticias HTML \n
    4 -> Pesquisar noticia por dia \n
    0 -> sair \n");

    $input = readline("Command: ");

   if ($input !== 5){
       menu($input);
       execMenu();
   }//e

}//execMenu

execMenu();
