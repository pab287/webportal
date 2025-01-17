<?php

require_once(__DIR__ . '/../model/models.php');

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="2.0",
 * )
 */

/**
 * @OA\Servers(
 *     @OA\Server(
 *         url="http://localhost/conyxph74/documentation/api/controller/controller.php",
 *         description="Main API server"
 *     )
 * )
 */


    $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    $action = end($request_uri);

    if (function_exists($action)) {
        $action();
    } else {
        http_response_code(404);
        echo json_encode(['error' => "Endpoint '{$action}' not found"]);
    }




    /**
         * @OA\Post(
         *     path="/user_login",
         *     operationId="user_login",
         *     summary="Hydrabilling Login",
         *     description="Authenticate a user based on the provided username and password. Returns user details if successful or a failed status if invalid.",
         *     @OA\RequestBody(
         *         required=true,
         *         description="User credentials for login",
         *         @OA\MediaType(
         *             mediaType="multipart/form-data",
         *             @OA\Schema(
         *                 required={"username", "password"},
         *                 @OA\Property(property="username", type="string", description="The username of the user", example="user"),
         *                 @OA\Property(property="password", type="string", description="The password of the user", example="pass")
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful login response with user information",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="info_array",
         *                 type="array",
         *                 description="List containing the user login response",
         *                 @OA\Items(
         *                     @OA\Property(property="status", type="string", example="success"),
         *                     @OA\Property(property="position", type="string", example="TEST POSITION"),
         *                     @OA\Property(property="employee_pic", type="string", example="5H7A9983.jpg"),
         *                     @OA\Property(property="firstname", type="string", example="test"),
         *                     @OA\Property(property="middlename", type="string", example="test"),
         *                     @OA\Property(property="lastname", type="string", example="test"),
         *                     @OA\Property(property="employee_id", type="integer", example=2308),
         *                     @OA\Property(property="username", type="string", example="username"),
         *                     @OA\Property(property="email", type="string", example="test_email@yahoo.com"),
         *                     @OA\Property(property="employee_status", type="string", example="Active")
         *                 )
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=401,
         *         description="Invalid username or password",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="info_array",
         *                 type="array",
         *                 @OA\Items(
         *                     @OA\Property(property="status", type="string", example="failed")
         *                 )
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=500,
         *         description="Internal Server Error"
         *     )
         * )
    */





?>
