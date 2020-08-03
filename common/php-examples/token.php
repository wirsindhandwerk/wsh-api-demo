<?php

/**
 * Get the token
 */
function getToken($host, $username, $password, $clientId, $clientSecret) {
    validateTokenRequest();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $host."/wsh-auth-webservice/oauth/token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "grant_type=password&username=".$username."&password=".$password."&client_id=".$clientId."&client_secret=".$clientSecret,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);

    if ($err) {
        die("Error Obtaining Token: ".$err);
    } else {
      $json = json_decode($response);
      $error = $json->error;  
      if ($error) {
          die("Error Obtaining Token: ".$response);
      } 
      $accessToken =  $json->access_token;
      if(!$accessToken) {
        die("Error Obtaining Token: ".$response);
      }
      return $accessToken; 
    }
}


function validateTokenRequest() {
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
    info("Request validated !!!");

}


function info($str) {
    echo $str."\n";
}

function error($str) {
    die($str."\n");
}



?>