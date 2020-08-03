

#!/bin/bash
usage() {
  echo "===== USAGE ===="
  echo "Enter POST JSON in postdata.txt"
  echo "Obtain access token from common/curl-examples/token.sh and enter in access_token.txt"
  echo "./curl.sh HOST=<host> TEMPLATE_ID=<template_id>"
} 

POSTDATA=$(<postdata.txt)
ACCESS_TOKEN=$(<access_token.txt)

while [[ "$#" > "0" ]]
do
  case $1 in
    (*=*) eval $1;;
  esac
shift
done

# echo POSTDATA $POSTDATA
# echo ACCESS_TOKEN $ACCESS_TOKEN

if [[ -z "$POSTDATA" || -z "$ACCESS_TOKEN" || -z "$HOST"  || -z "$TEMPLATE_ID" ]]; then
  usage 
  exit 1;
fi

curl -X POST \
  ${HOST}/wsh-print-webservice/api/v1/pdf/${TEMPLATE_ID} \
  -H "authorization: Bearer ${ACCESS_TOKEN}" \
  -H 'content-type: application/json' \
  -d "${POSTDATA}" 
  