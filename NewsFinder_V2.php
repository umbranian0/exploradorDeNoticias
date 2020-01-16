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

    // --Criar e abrir um HTML no browser
    public static function verNoticiasHTML(array $pArrayNoticias){
        $myFileName = self::linksToHtml_retCaminhoFicheiro($pArrayNoticias);

        self::criarFicheiro($myFileName);

    }//verNoticiasHTML

    public function verifyAnchorFilteringArray(array $pArrayToFilter){
        $filtredArr = [];
        foreach($pArrayToFilter as $item){
            if( strlen($item["anchor"]) >= 40  ) {
                $filtredArr[] = $item;
            }
        }

        return AmUtil::super_unique($filtredArr);
    }//fossBytesFilteringArray

    public static function criarFicheiro(string $pMyFileName){

        $myfile = fopen($pMyFileName, "r");
        //echo ("a tentar abrir ficheiro");
        if( !file_exists($pMyFileName ))
            echo ("Unable to open file!");
        //echo fread($myfile,filesize($myFileName));
        fclose($myfile);

        //echo("a abrir HTML");
        $browser_PATH = MOZZILA_PATH;
        //abre o ficheiro com o browser
        system(
            "\"$browser_PATH\" \"$pMyFileName\"",
            $allOutput
        );
    }//criarFicheiro

    public static function pesquisarNoticiaFonteExterna(string $pUrlFonteExterna){

        $bIsValidUrl =  AmUtil::isValidHttpURL($pUrlFonteExterna);

        if ($bIsValidUrl) {
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

                $results = $NewsFinder->verifyAnchorFilteringArray($aPairs) ;

                //filtrar duplicados
                $results = AmUtil::super_unique($results);


                return $results;
            }
        }
        else{

            echo("unsupported URL!!");
        }

    }//pesquisarNoticiasFonteExterna
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
