<?php
/*
 * Pretende-se uma fábrica de objetos capazes de consumirem
 * binários em boards da rede 4chan.org
 * Iremos ilustrar com um cliente para consumo de board
 * "wall papers" publicada em
 * https://boards.4chan.org/wg/
 *
 * Features desejadas:
 * - identificar binários divulgados na board
 * - download dos binários
 * -- para uma pasta configurável
 * --- baseada no nome da board, auto-datada
 * -- evitar repetições
 * --- memória de downloads passados
 * --- tecnologia para mem não volátil: ficheiros TSV
 * --- MySQL "my sequel"
 * -- oportunidades
 * --- recolha de comments?
 */

require_once "AmUtil.php";

class FourChanBoardConsumer{
    private $mBoard;

    const DOWNLOAD_FOLDER = "dls";
    const URL_BASE = "https://boards.4chan.org";

    public function __construct(
        string $pStrBoardName
    )
    {
        $this->mBoard = $pStrBoardName;

        /*
         * na primeira construção, a pasta é criada
         * em construções seguintes causa um warning
         * pq não é possível fazer o que está feito
         * @ silencia esse warning
         */
        $bTrueOnSuccessFalseOtherwise =
            @mkdir(self::DOWNLOAD_FOLDER);
    }//__construct

    const MIN_PAGE = 1;
    const MAX_PAGE = 10;
    public function allValidUrls(

    ){
        $aValidUrl = [];

        for ( $page = self::MIN_PAGE; $page <= self::MAX_PAGE; $page++){
            $strUrl = sprintf(
                "%s/%s/%s",
            self::URL_BASE,
            $this->mBoard, ($page == self::MIN_PAGE) ? "" : $page
            );
            $aValidUrl [] = $strUrl;
        }

        return $aValidUrl;
    }//allValidUrl


    public function allHyperlinksAtBoardUrl(
        string $pStrUrl
    ){
        $html = AmUtil::consumeUrl($pStrUrl);
             $aPairsAnchorRef =  AmUtil::getHyperlinksFromDOMFromSource($html);
             return $aPairsAnchorRef;
    }//allHyperlinksAtBoardUrl

    public function allExistingImages(
        $paPairs
    ){
        foreach ($paPairs as $pair){
            $anchor = $pair[AmUtil::ANCHOR];
            $href = $pair[AmUtil::HREF];
            $bHREFendsInSupportedFormat  =
                AmUtil::sttringEndsInOneOfThere(
                    $href , self::SUPPORTED_IMAGE_FORMATS
                );
            if ($bHREFendsInSupportedFormat){
                $aImagesFound[] = $pair;
            }//if
        }//foreach
        return $aImagesFound;
    }//allExistingImages

}//FourChanBoardConsumer
