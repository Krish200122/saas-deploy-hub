#!/bin/bash

SUBDOMAIN=$1      
DOMAIN="tamucommerce.in"
IP=$(curl -s ifconfig.me)

curl -X PUT \
  -H "Content-Type: application/json" \
  -H "Authorization: sso-key $GODADDY_API_KEY:$GODADDY_API_SECRET" \
  -d "[ { \"data\": \"$IP\", \"ttl\": 600 } ]" \
  "https://api.godaddy.com/v1/domains/$DOMAIN/records/A/$SUBDOMAIN"

echo "✅ Subdomain $SUBDOMAIN.$DOMAIN -> $IP created"
