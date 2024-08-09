<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use function Pest\Laravel\call;

uses(RefreshDatabase::class);

class ApiTestHelper
{
    public static string $userUuid = '517e29ec-96ce-4b7d-b3bd-8415c23f10a5';

    public static function makeGetRequest(string $uri): TestResponse
    {
        return call(
            method: 'GET',
            uri: $uri,
            parameters: [],
            cookies: [],
            files: [],
            server: [
                "HTTP_ACCEPT" => "application/json",
                "HTTP_CLEEMA_INSTALL_ID" => self::$userUuid,
                "HTTP_AUTHORIZATION" => "bearer ".env('IOS_LOCAL_ACCESS_TOKEN')
            ],
            content: null
        );
    }

    public static function makePostRequest(string $uri, array $parameters): TestResponse
    {
        return call(
            method: 'POST',
            uri: $uri,
            parameters: $parameters,
            cookies: [],
            files: [],
            server: [
                "HTTP_ACCEPT" => "application/json",
                "HTTP_CLEEMA_INSTALL_ID" => self::$userUuid,
                "HTTP_AUTHORIZATION" => "bearer ".env('IOS_LOCAL_ACCESS_TOKEN')
            ],
            content: null
        );
    }

    public static function makePatchRequest(string $uri, array $parameters = []): TestResponse
    {
        return call(
            method: 'PATCH',
            uri: $uri,
            parameters: $parameters,
            cookies: [],
            files: [],
            server: [
                "HTTP_ACCEPT" => "application/json",
                "HTTP_CLEEMA_INSTALL_ID" => self::$userUuid,
                "HTTP_AUTHORIZATION" => "bearer ".env('IOS_LOCAL_ACCESS_TOKEN')
            ],
            content: null
        );
    }

    public static function makeDeleteRequest(string $uri): TestResponse
    {
        return call(
            method: 'DELETE',
            uri: $uri,
            parameters: [],
            cookies: [],
            files: [],
            server: [
                "HTTP_ACCEPT" => "application/json",
                "HTTP_CLEEMA_INSTALL_ID" => self::$userUuid,
                "HTTP_AUTHORIZATION" => "bearer ".env('IOS_LOCAL_ACCESS_TOKEN')
            ],
            content: null
        );
    }

}
