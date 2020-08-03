
#!/bin/bash
usage() {
  echo "===== USAGE ===="
  echo "./token.sh USERNAME=<username> PASSWORD=<password> CLIENT_ID=<client_id> CLIENT_SECRET=<client_secret> HOST=<host>"
} 

while [[ "$#" > "0" ]]
do
  case $1 in
    (*=*) eval $1;;
  esac
shift
done

if [[ -z "$USERNAME" || -z "$PASSWORD"  || -z "$CLIENT_ID"  || -z "$CLIENT_SECRET"  || -z "$HOST" ]]; then
  usage 
  exit 1;
fi

curl -X POST \
  ${HOST}/wsh-auth-webservice/oauth/token \
  -H 'content-type: application/x-www-form-urlencoded' \
  -d 'grant_type=password&username='${USERNAME}'&password='${PASSWORD}'&client_id='${CLIENT_ID}'&client_secret='${CLIENT_SECRET}
