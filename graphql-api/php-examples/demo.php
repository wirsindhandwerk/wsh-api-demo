<?php
error_reporting(E_ALL);
header('Content-type:text/plain;charset=utf-8');
info("Starting process");

if (!file_exists(dirname(__FILE__) . '/config.php')) {
    error("Please create a config.php and enter your settings there.");
    die();
}
$config = include dirname(__FILE__) . '/config.php';
$username = $config['username'];
$password = $config['password'];
$clientId = $config['clientId'];
$clientSecret = $config['clientSecret'];
$host = $config['host'];
$groupId = $config['groupId'];

if (!validateConfig($config)) {
    error("Config is invalid.");
    die();
}

info("Config looks valid.");


// Read the query
$requestQueryFromFile = file_get_contents("graphQLRequest.txt");

$requestQueryFromFile = str_replace('{GROUP_ID}', $groupId, $requestQueryFromFile);

$graphQLQuery = new \stdClass();
$graphQLQuery->query = $requestQueryFromFile;
$graphQLQueryObj = json_encode($graphQLQuery);

info("Trying to Obtain Token...");

// Get Access Token
$accessToken = getToken($host, $username, $password, $clientId, $clientSecret);
if (!$accessToken) {
    error("Could not obtain token");
    die();
}
info(sprintf("Token obtained, Calling GraphQL API"));

// Run GraphQL Query
$graphQLResponse = callGraphQLApi($host, $accessToken, $graphQLQueryObj);

info("GraphQL Response: " . json_encode($graphQLResponse, JSON_PRETTY_PRINT));



// common functions.




function callGraphQLApi($host, $accessToken, $query)
{
    $maxRetries = 10;


    for ($numRequests = 0; $numRequests <= $maxRetries; $numRequests++) {
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $host . "/wsh-api-webservice/v3/api",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $accessToken,
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if (!$err) {
            return json_decode($response);
        }

        error(sprintf("cURL error #%s: %s", $numRequests, $err));
        $numRequests++;
    } 
        
    error("Maximum number of retries reached. API failure");
    return null;
    
}

/**
 * fetch a fresh access token from auth webservice.
 */
function getToken($host, $username, $password, $clientId, $clientSecret)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $host . "/wsh-auth-webservice/oauth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=password&username=" . $username . "&password=" . $password . "&client_id=" . $clientId . "&client_secret=" . $clientSecret,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        die("Error Obtaining Token: " . $err);
    } else {
        $json = json_decode($response);
        $error = $json->error;
        if ($error) {
            error("Error Obtaining Token: " . $response);
            return null;
        }
        $accessToken =  $json->access_token;
        return $accessToken;
    }
}

/**
 * validates the given config for required values.
 */
function validateConfig($config)
{
    $requiredKeys = array(
        'username', 'password', 'clientId', 'clientSecret', 'host'
    );

    foreach ($requiredKeys as $k) {
        if (!array_key_exists($k, $config) || trim($config[$k]) === '') {
            error(sprintf("Config key [%s] is mandatory.", $k));
            return false;
        }
    }
    return true;
   
}


function info($str)
{
    printf("[%s] INFO: %s\n", date('Y-m-d H:i:s'), $str);
}

function error($str)
{
    printf("[%s] ERROR: %s\n", date('Y-m-d H:i:s'), $str);
}
