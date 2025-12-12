<?php

Flight::group('/auth', function() {

    /**
     * @OA\Post(
     * path="/auth/register",
     * tags={"auth"},
     * summary="Register new user"
     * )
     */
    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();
        // Call the service [cite: 269]
        $response = Flight::auth_service()->register($data);

        if ($response['success']) {
            Flight::json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(400, json_encode([
                'success' => false,
                'message' => $response['error']
            ]));
        }
    });

    /**
     * @OA\Post(
     * path="/auth/login",
     * tags={"auth"},
     * summary="Login user"
     * )
     */
    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();
        // Call the service [cite: 300]
        $response = Flight::auth_service()->login($data);

        if ($response['success']) {
            Flight::json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $response['data'] // This contains the token
            ]);
        } else {
            Flight::halt(401, json_encode([
                'success' => false,
                'message' => $response['error']
            ]));
        }
    });

});
?>