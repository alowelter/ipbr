<?php
//-------[INICIO]--[www.alowelter.com.br]--------------
/*
* AloWelter - Filtragem de IP por pais.
*
* Função: bloqueio de acessos de fora do Brasil.
*/
function getLocationInfoByIp() {
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    $result  = array('country'=>'', 'city'=>'');
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=".$ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = @json_decode(curl_exec($ch));
    curl_close($ch);

    if($output){
        $result['country'] = $output->geoplugin_countryCode;
    }
    // Salva logs em arquivo
    // file_put_contents('sec.log', $ip.' - '.$result['country'].' - '.date('H:m d/m/Y').PHP_EOL, FILE_APPEND);
  
    return $result;
}

$data = getLocationInfoByIp();
if($data['country'] != 'BR' && !strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot") ) {
    http_response_code(401); // retorna o Erro "Não autorizado"
    die;
}

// Segue normalmente o carregamento
?>
