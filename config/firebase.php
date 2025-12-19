<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID from the Firebase Console.
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to your Firebase service account JSON file.
    | Store the file in storage/app/firebase/service-account.json
    | and set the path in your .env file.
    |
    */
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),

];
