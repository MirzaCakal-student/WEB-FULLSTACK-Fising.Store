<?php
 //finished
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(
 *   name="orders",
 *   description="Customer orders"
 * )
 */

/**
 * @OA\Get(
 *   path="/orders",
 *   tags={"orders"},
 *   summary="Get all orders",
 *   @OA\Response(response=200, description="List of orders")
 * )
 */
Flight::route('GET /orders', function () {
    Flight::json([
        'success' => true,
        'data'    => Flight::orderService()->get_all()
    ]);
});

/**
 * @OA\Get(
 *   path="/orders/{id}",
 *   tags={"orders"},
 *   summary="Get order by ID",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Order found")
 * )
 */
Flight::route('GET /orders/@id', function ($id) {
    $order = Flight::orderService()->get_by_id($id);
    if ($order) {
        Flight::json(['success' => true, 'data' => $order]);
    } else {
        Flight::json(['success' => false, 'message' => 'Order not found'], 404);
    }
});

/**
 * @OA\Post(
 *   path="/orders",
 *   tags={"orders"},
 *   summary="Create new order",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"user_id","total_amount"},
 *       @OA\Property(property="user_id", type="integer", example=1),
 *       @OA\Property(property="status", type="string", example="pending"),
 *       @OA\Property(property="total_amount", type="number", format="float", example=120.50)
 *     )
 *   ),
 *   @OA\Response(response=200, description="Order created")
 * )
 */
Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::orderService()->add($data)
    ]);
});

/**
 * @OA\Put(
 *   path="/orders/{id}",
 *   tags={"orders"},
 *   summary="Update order",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       @OA\Property(property="status", type="string"),
 *       @OA\Property(property="total_amount", type="number", format="float")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Order updated")
 * )
 */
Flight::route('PUT /orders/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::orderService()->update($data, $id, 'order_id')
    ]);
});

/**
 * @OA\Delete(
 *   path="/orders/{id}",
 *   tags={"orders"},
 *   summary="Delete order",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Order deleted")
 * )
 */
Flight::route('DELETE /orders/@id', function ($id) {
    Flight::orderService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Order deleted']);
});
