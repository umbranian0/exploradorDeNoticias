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

    //cria ficheiro html para os dados
    public static function dumpToHtml(
        $pLinks
    ){
        $ret = "<ol>";
        foreach($pLinks as $link){
            $anchor = $link["anchor"];
            $href = $link["href"];
            $str = sprintf("<li><a href='%s'>%s</a></li>".PHP_EOL, $anchor, $href);
            $ret.=$str;
        }
        $ret .="</ol>";

        if (!file_exists('htmlPresent.html'))
        {
            $handle = fopen('../htmlPresent.html','w+');
            fwrite($handle,$ret);
            fclose($handle);
        }
        return 'htmlPresent.html';
    }//dumpToHtml
}
