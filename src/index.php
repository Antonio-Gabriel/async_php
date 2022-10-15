<?php

require_once dirname(__DIR__). "/vendor/autoload.php";

function index() {
    $client = new GuzzleHttp\Client([
        'base_uri' => 'https://jsonplaceholder.typicode.com'
    ]);    

    $response = $client->request('GET', '/users', ['debug' => false]);

    $users = json_decode($response->getBody());

    $results = [];

    # This process is sync and I will alter to async
    // foreach ($users as $user) {        
    //     $todoResponse = $client->request('GET', "/users/{$user->id}/todos");

    //     $todos = json_decode($todoResponse->getBody());

    //     $user->todos = $todos;

    //     $results[] = $user;        
    // }    
        
    Co\run(function() use ($users, &$results) {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com'
        ]); 

        foreach ($users as $user) {
            Co\go(function() use ($client, $user, &$results){
                $todoResponse = $client->request('GET', "/users/{$user->id}/todos");

                $todos = json_decode($todoResponse->getBody());
                $user->todos = $todos;

                $results[] = $user;
            });
        }
    });
    
    return APIResponse($results);
}

function APIResponse($body) {
    $headers = [
        'Content-Type'                  => 'application/json',
        'Access-Control-Allow-Origin'   => '*',
        'Access-Control-Allow-Headers'  => 'Content-Type',
        'Access-Control-Allow-Methods'  => 'OPTIONS,POST,GET',
    ];

    return json_encode([
        'statusCode' => 200,
        'headers'    => $headers,
        'body'       => $body
    ]);
}

echo index();