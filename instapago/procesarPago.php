<?php

/*
 * Filename: sample.php
 * Description: Ejemplo para consumo de la API de instapago
 * @author Personal de Instapago
 * @version 1.5.4
 */

$Concepto       = trim($_POST["concepto"]);
$CardHolder     = trim($_POST["cardHolder"]);
$CardHolderId   = trim($_POST["cardHolderId"]);
$CardNumber     = trim($_POST["cardNumber"]);
$CVC            = trim($_POST["cardCVC"]);
$ExpirationDate = trim($_POST["cardExpiry"]);
$Monto          = trim($_POST["monto"]);

// https://www.chriswiegman.com/2014/05/getting-correct-ip-address-php/
function get_ip() {
    if ( function_exists( 'apache_request_headers' ) ) {
        $headers = apache_request_headers();
    } else {
        $headers = $_SERVER;
    }
    if (array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
        $the_ip = $headers['X-Forwarded-For'];
    }elseif (array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
        $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    }else {
        $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
    }
    return $the_ip;
}
$url = 'https://api.instapago.com/payment';
$fields = array(
    "KeyID"             => "B9B4E3C8-BE68-4558-8910-63E628D179DD", //required
    "PublicKeyId"       => "241442708fcd76d1b26fb33a7a5a2fd9", //required
    "Amount"            => $Monto, //required
    "Description"       => $Concepto, //required
    "CardHolder"        => $CardHolder, //required
    "CardHolderId"      => $CardHolderId, //required
    "CardNumber"        => $CardNumber, //required
    "CVC"               => $CVC, //required
    "ExpirationDate"    => $ExpirationDate, //required
    "StatusId"          => "2", //required
    "IP"                => get_ip() //required
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url );
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);
curl_close ($ch);
$obj = json_decode($server_output);
$code = $obj->code;
$msgerror = '';
switch($code){
    case 400:
        $msgerror= 'Error al validar los datos enviados..';
        break;
    case 401:
        $msgerror= 'Error de autenticación, ha ocurrido un error con las llaves utilizadas..';
        break;
    case 403:
        $msgerror= 'Pago Rechazado por el banco..';
        break;
    case 500:
        $msgerror= 'Ha Ocurrido un error interno dentro del servidor..';
        break;
    case 503:
        $msgerror= 'Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo..';
        break;
    case 201:
        $msg_banco  = $obj->message;
        $voucher  = $obj->voucher;
        $voucher = html_entity_decode($voucher);
        $id_pago  = $obj->id;
        $reference  = $obj->reference;
        break;
    default:
        $msgerror='Error '.$code.' inesperado, Imposible determinar. Contacte al Administrador';
        break;
}
if(!empty($msgerror)){
    echo'
        <div class="alert alert-danger" role="alert"><p>'.$msgerror.'</p></div>';
    die();
}else{
    echo'
    <div class="panel panel-success">
        <div class="panel-heading">Respuesta Transacción</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-7 col-md-7">
                        <h3>Respuesta del Banco</h3>
                        <p>
                            <strong>Mensaje del Banco</strong>: '.$msg_banco.'<br/>
                            <strong>ID del Pago</strong>: '.$id_pago.'<br/>
                            <strong>ID Referencia</strong>: '.$reference.'<br/>
                        </p>
                    </div>
                    <div class="col-xs-5 col-md-5 pull-right">
                        <h3>Voucher</h3>
                        '.$voucher.'
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

?>
