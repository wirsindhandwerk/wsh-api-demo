<?php

require  '../../common/php-examples/token.php';

info("Starting process...");

validateRequest();

$request = file_get_contents("request.json");
$requestJson = json_decode($request);
$username = $requestJson->username;
$password = $requestJson->password;
$clientId = $requestJson->clientId;
$clientSecret = $requestJson->clientSecret;
$host = $requestJson->host;
$templateId = $requestJson->templateId;

// Read the query
$requestFromFile = file_get_contents("request.txt");

info("Trying to Obtain Token...");

// Get Access Token
$accessToken = getToken($host, $username, $password, $clientId, $clientSecret);

# info("Token Obtained, Calling API".$accessToken);

// Call API
$graphQLResponse = callApi($host, $accessToken, $templateId, $requestFromFile);
info("API Response: ".$graphQLResponse);

function callApi($host, $accessToken, $templateId, $postBody) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $host."/wsh-print-webservice/api/v1/pdf/".$templateId,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $postBody,
      CURLOPT_HTTPHEADER => array(
        "authorization: Bearer ".$accessToken,
        "cache-control: no-cache",
        "content-type: application/json",
        "postman-token: c2919dc7-b293-eb4e-2f3e-2d809f7f5b24"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
}


function validateRequest() {
    info("Validating request...");
    $request = file_get_contents("request.json");
    $requestJson = json_decode($request);
    
    $username = $requestJson->username;
    if(!$username) {
        error("Username is Mandatory");
    }
    
    $password = $requestJson->password;
    if(!$password) {
        error("password is Mandatory");
    }
    
    $clientId = $requestJson->clientId;
    if(!$clientId) {
        error("clientId is Mandatory");
    }
    
    $clientSecret = $requestJson->clientSecret;
    if(!$clientSecret) {
        error("clientSecret is Mandatory");
    }

    $host = $requestJson->host;
    if(!$host) {
        error("host is Mandatory");
    }
   
    $templateId = $requestJson->templateId;
    if(!$templateId) {
        error("templateId is Mandatory");
    }
    info("Request validated !!!");

}

?>