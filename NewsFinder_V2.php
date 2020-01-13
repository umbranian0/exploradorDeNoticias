<?php
require_once "AmUtil.php";

class NewsFinder_V2{
    //atributos
    private $boardName;
    private $URL_BASE ;
    //constantes



    //construtor para a class de noticias
    public function __construct(
        string $pBoardName,
        string $pURL_BASE
    ){
        //atribuir nome da board
        $this->boardName = $pBoardName;
        $this->URL_BASE = $pURL_BASE;
    }

    const MIN_PAGE = 1;
    const MAX_PAGE = 10;

    public function allValidUrls(){
        $aValidUrls = [];

        for ($page = self::MIN_PAGE; $page<= self::MAX_PAGE; $page++){
            $strUrl = sprintf(
                "%s",
                $this->URL_BASE,
                $this->boardName,
                ($page == self::MIN_PAGE) ? "" : $page
            );
            $aValidUrls[] = $strUrl;
        }//for

        return $aValidUrls;
    }//allValidUrls

    public function allHyperlinksAtBoardUrl(
        string $pStrUrl
    ){
        $html = AmUtil::consumeUrl($pStrUrl);
        $aPairsAnchorHref =
            AmUtil::getHyperlinksFromDOMFromSource($html);

        return $aPairsAnchorHref;
    }//allHyperlinksAtBoardUrl

}
