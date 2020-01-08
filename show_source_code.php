<?php
/*
 * app de consulta de noticias do dia do observador.pt
 *
 */
var_dump($argv);
$strHtmlSourceCode =
    file_get_contents("http://arturmarques.com/");

define("QUANTIDADE_MIN_DE_ARG",2);
define("INDEX_DO_URL", 1);
//validacao do input de entrada
$bRecebiUmUrlValido = !empty(
($argc >= QUANTIDADE_MIN_DE_ARG )
    &&
(!empty($argv[INDEX_DO_URL])));
//fim validacao

if($bRecebiUmUrlValido ){
  $url = $argv[1];
  $srcCode = file_get_contents($url);
  $srcCode = trim($srcCode);

  echo strlen($srcCode) === 0 ? "Nao consegue obter source code" : $srcCode;
}
else{
    echo "url invalido";
}


