#!/bin/bash

bearer_token=$1

strapi_base_url="https://api.cleema.app/api/"
laravel_base_url="http://localhost/api/"
strapi_output_file="strapi_output.json"
laravel_output_file="laravel_output.json"

endpoints=("trophies?populate=*" "news-entries?populate=*" "challenges?populate=*")
filenames=("trophies" "news-entries" "challenges")

# Make 3 HTTP requests with the bearer token
for i in {0..2}; do
    # Replace the following with your actual request payload and headers
    request_payload="{\"key\": \"value\"}"
    headers="Authorization: Bearer $bearer_token"

    api_endpoint="${strapi_base_url}${endpoints[i]}"

    # Make the HTTP request with cURL
    # shellcheck disable=SC1073
    # shellcheck disable=SC1009
    response=$(curl -s -X GET \
    -H "Content-Type: application/json" \
    -H "Host: api.cleema.app" \
    -H "Connection: keep-alive" \
    -H "cleema-install-id: B88E5EB2-F1ED-4C86-8640-CF7341CBAF00" \
    -H "Accept: */*" \
    -H "User-Agent: Cleema/1149 CFNetwork/1490.0.4 Darwin/23.2.0" \
    -H "$headers" \
    -d "$request_payload" \
    "$api_endpoint")

    # Display the response for each request
    echo "response"
    echo "$api_endpoint"
    echo "$response" | jq '.' > "${filenames[i]}.$strapi_output_file"
done

# Make 3 HTTP requests with the bearer token
for i in {0..2}; do
    api_endpoint="${laravel_base_url}${endpoints[i]}"

    # Make the HTTP request with cURL
    response=$(curl -s -X GET \
    -H "Content-Type: application/json" \
    -H "$headers" \
    -d "$request_payload" \
    "$api_endpoint")

    # Display the response for each request
    echo "$api_endpoint"
    echo "$response" | jq '.' > "${filenames[i]}.$laravel_output_file"
done
