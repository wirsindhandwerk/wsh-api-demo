<?php

info("Starting process");

validateRequest();

$request = file_get_contents("request.json");
$requestJson = json_decode($request);
$username = $requestJson->username;
$password = $requestJson->password;
$clientId = $requestJson->clientId;
$clientSecret = $requestJson->clientSecret;
$host = $requestJson->host;

// Read the query
$requestQueryFromFile = file_get_contents("graphQLRequest.txt");
$graphQLQuery->query = $requestQueryFromFile;
$graphQLQueryObj = json_encode($graphQLQuery);

info("Trying to Obtain Token...");

// Get Access Token
$accessToken = getToken($host, $username, $password, $clientId, $clientSecret);

info("Token Obtained, Calling GraphQL API");

// Run GraphQL Query
$graphQLResponse = callGraphQLApi($host, $accessToken, $graphQLQueryObj);
info("GraphQL Response: ".$graphQLResponse);

function callGraphQLApi($host, $accessToken, $query) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $host."/wsh-api-webservice/v3/api",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $query,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$accessToken,
            "cache-control: no-cache",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
}

/**
 * Get the token
 */
function getToken($host, $username, $password, $clientId, $clientSecret) {
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
      return $accessToken; 
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
    info("Request validated !!!");

}

function getQuery($query) {
    return htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
}

function info($str) {
    echo $str."\n";
}

function error($str) {
    die($str."\n");
}

?>