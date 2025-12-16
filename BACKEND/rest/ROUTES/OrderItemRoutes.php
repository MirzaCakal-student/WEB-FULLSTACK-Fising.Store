<?php
 //finished
use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/orders",
 *     tags={"orders"},
 *     summary="Get all orders (optionally by user)",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer"),
 *         description="Filter orders by user ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order list"
 *     )
 * )
 */
Flight::route('GET /orders', function () {
    $user_id = Flight::request()->query['user_id'] ?? null;
    try {
        $orders = Flight::orderService()->get_orders($user_id);
        Flight::json(['success' => true, 'data' => $orders]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Get order by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Order ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order data"
 *     )
 * )
 */
Flight::route('GET /orders/@id', function ($id) {
    try {
        $order = Flight::orderService()->get_order_by_id($id);
        Flight::json(['success' => true, 'data' => $order]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/orders",
 *     tags={"orders"},
 *     summary="Create new order",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","total_price"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="status", type="string", example="Pending"),
 *             @OA\Property(property="total_price", type="number", example=199.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created"
 *     )
 * )
 */
Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    try {
        $created = Flight::orderService()->add_order($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Update order",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Completed"),
 *             @OA\Property(property="total_price", type="number")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order updated"
 *     )
 * )
 */
Flight::route('PUT /orders/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::orderService()->update_order($id, $data);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Delete order",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order deleted"
 *     )
 * )
 */
Flight::route('DELETE /orders/@id', function ($id) {
    try {
        $res = Flight::orderService()->delete_order($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
