<?php

require  '../../common/php-examples/token.php';

info("Starting process");

$request = file_get_contents("request.json");
$requestJson = json_decode($request);
$username = $requestJson->username;
$password = $requestJson->password;
$clientId = $requestJson->clientId;
$clientSecret = $requestJson->clientSecret;
$host = $requestJson->host;

// Read the query
$requestQueryFromFile = file_get_contents("graphQLRequest.txt");
$graphQLQuery = new \stdClass();
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

?>