#!/bin/bash
usage() {
  echo "===== USAGE ===="
  echo "Enter graphQL query in query.txt"
  echo "Obtain access token from token.sh and enter in access_token.txt"
  echo "./curl.sh HOST=<host>"
} 

QUERY=$(<query.txt)
ACCESS_TOKEN=$(<access_token.txt)

while [[ "$#" > "0" ]]
do
  case $1 in
    (*=*) eval $1;;
  esac
shift
done

if [[ -z "$QUERY" || -z "$ACCESS_TOKEN" || -z "$HOST" ]]; then
  usage 
  exit 1;
fi

curl -X POST \
  -H "authorization: Bearer ${ACCESS_TOKEN}" \
  -H 'content-type: application/json' \
  -d "${QUERY}" \
  ${HOST}/wsh-api-webservice/v3/api


