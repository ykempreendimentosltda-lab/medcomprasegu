<?php

header('Content-Type: application/json');

$PUBLIC_KEY = 'kauanpereirabohrer2_vd0gs1uhpr24t84a';
$SECRET_KEY = 'emxqdzjh3qlnbwd3a3xffih8pyx0x15wy7p8bcr2r57954ki0e0ee8kxia57kyc9';

$data = json_decode(file_get_contents("php://input"), true);

$identifier = uniqid('pedido_');

$valor = 99.00; // coloque o valor do seu checkout

$body = [
    "identifier" => $identifier,
    "amount" => $valor,
    "client" => [
        "name" => $data["nome"] ?? "",
        "email" => $data["email"] ?? "",
        "phone" => $data["telefone"] ?? "",
        "document" => $data["cpf"] ?? ""
    ],
    "products" => [
        [
            "id" => "produto1",
            "name" => "Produto",
            "quantity" => 1,
            "price" => $valor
        ]
    ]
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://app.sigilopay.com.br/api/v1/gateway/pix/receive",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "x-public-key: ".$PUBLIC_KEY,
        "x-secret-key: ".$SECRET_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($body)
]);

$response = curl_exec($curl);

if(curl_errno($curl)){
    echo json_encode([
        "ok"=>false,
        "erro"=>curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

$retorno = json_decode($response,true);

if(
    isset($retorno["status"]) &&
    $retorno["status"]=="OK" &&
    isset($retorno["pix"]["code"])
){

}else{

    echo json_encode([
        "ok"=>false,
        "erro"=>$retorno
    ]);

}