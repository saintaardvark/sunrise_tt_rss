#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

curl -s -X POST 'https://nrc.canada.ca/en/research-development/products-services/software-applications/sun-calculator/' \
  --data-urlencode "language=en" \
  --data-urlencode "olat=0" \
  --data-urlencode "elong=0" \
  --data-urlencode "calcSubject=ss-year-txt" \
  --data-urlencode "calcMethod=byCity" \
  --data-urlencode "calcDate=$(date +%Y-%m-%d)" \
  --data-urlencode "city=VANCOUVER BC" \
  --data-urlencode "observ=" \
  --data-urlencode "degrees-latitude=" \
  --data-urlencode "minutes-latitude=" \
  --data-urlencode "latitude=" \
  --data-urlencode "degrees-longitude=" \
  --data-urlencode "minutes-longitude=" \
  --data-urlencode "longitude=" \
  --data-urlencode "timezone=" \
  -o data/sunrise.csv

cp data/sunrise.csv vancouver_sunrise/sunrise.csv
echo "Updated sunrise.csv with data fetched on $(date +%Y-%m-%d)"
