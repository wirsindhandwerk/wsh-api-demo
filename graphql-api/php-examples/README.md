# General API Prerequisites

* A valid, active user on wirsindhandwerk.de
* Get in touch with the [admin](alexander.onea@wirsindhandwerk.de), we will provide the further details to you.

## Required configuration values

| Config value | Description | 
|--------------|-------------|
| `username`   | Your WSH Username / Login |
| `password`   | Your WSH password |
| `clientId`   | Client ID provided by administrators | 
| `clientSecret` | Client Secret provided by administators |
| `groupId` | The public Id of the WSH group you are accessing. | 

## Prerequisites for the GraphQL API Demo

* PHP should be installed

## Running the GraphQL API Demo
* copy `config.sample.php` to `config.php`
* Add the authorization request parameters in `config.php`
* review and modify the GraphQL query in `graphQLRequest.txt`
* Run: `php demo.php` 


# Resources

* GraphiQL Frontend for [dev](https://dev.wirsindhandwerk.de/wsh-api-webservice/v3doc) and [prod](https://www.wirsindhandwerk.de/wsh-api-webservice/v3doc )
* Groups Dashboard for [dev](https://dev.wirsindhandwerk.de/dashboard/groups) and [prod](https://www.wirsindhandwerk.de/dashboard/groups)
* [wirsindhandwerk CI/CD](https://invis.io/3UQ2U03KBFT)
* [wirsindhandwerk CI/CD guideline for partners](https://invis.io/KM10Q3LUVR2Y)









