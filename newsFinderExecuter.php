<?php
//wg2.php
require_once "NewsFinder_V2.php";
define("FOSSBYTES_URL", "https://fossbytes.com/");

function fossbytesUrlTest(){

    $FossBytesNewFinder = new NewsFinder_V2("news" , FOSSBYTES_URL);
    $aAllValidUrlsForTheBoard = $FossBytesNewFinder->allValidUrls();

    $firstPage = $aAllValidUrlsForTheBoard[0];
    $aPairs = $FossBytesNewFinder->allHyperlinksAtBoardUrl($firstPage);

    $results = fossBytesFilteringArray($aPairs) ;

var_dump ($results);
}//test20191204_1520

function fossBytesFilteringArray(array $pArrayToFilter){
    $filtredArr = [];
    foreach($pArrayToFilter as $item){
        if(strlen($item["anchor"]) >= 40)
            $filtredArr[] = $item;
    }
    return $filtredArr;
}


fossbytesUrlTest();
