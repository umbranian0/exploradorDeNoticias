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
    public static function linksToHtml_retCaminhoFicheiro(
        $pLinks
    ){
        //base HTML
        $ret = "<!doctype html><html>
        <head><meta charset='utf-8'>
        <title>Noticiário</title>
        <H3>Noticiário</H3>
        </head><body>";

        //criação do formato do HTML
        $ret .= "<ol>";
        foreach($pLinks as $link){
            $anchor = $link["anchor"];
            $href = $link["href"];
           // $str = sprintf("<li><a anchor='%s'>%s</a></li>".PHP_EOL,  $href,$anchor);
            if(strlen($anchor) > 0 )
            $str = sprintf("<li><a href='$href'>%s</a></li> \n".PHP_EOL, $anchor, $href);

            $ret.=$str;
        }
        $ret .="</ol>";
        $ret .="</body></html>";

        //criação do ficheiro
        $nomeFicheiro = date('YmdHis').'_noticias.html';
        $caminho = false ;
        if (!file_exists($nomeFicheiro))
        {
            $handle = fopen($nomeFicheiro,'w');
            $caminho = realpath($nomeFicheiro);
            fwrite($handle,$ret);
            fclose($handle);
        }

        return $caminho;
    }//dumpToHtml
}
