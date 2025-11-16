<?php
use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/cart-items",
 *     tags={"cart"},
 *     summary="Get cart items (optionally filtered by user)",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer"),
 *         description="Filter cart by user ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart items list"
 *     )
 * )
 */
Flight::route('GET /cart-items', function () {
    $user_id = Flight::request()->query['user_id'] ?? null;
    try {
        $items = Flight::cartItemService()->get_cart_items($user_id);
        Flight::json(['success' => true, 'data' => $items]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/cart-items",
 *     tags={"cart"},
 *     summary="Add item to cart",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","product_id","quantity"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=3),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item added to cart"
 *     )
 * )
 */
Flight::route('POST /cart-items', function () {
    $data = Flight::request()->data->getData();
    try {
        $created = Flight::cartItemService()->add_cart_item($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/cart-items/{id}",
 *     tags={"cart"},
 *     summary="Update cart item quantity",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Cart item ID"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart item updated"
 *     )
 * )
 */
Flight::route('PUT /cart-items/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::cartItemService()->update_cart_item($id, $data);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/cart-items/{id}",
 *     tags={"cart"},
 *     summary="Delete cart item",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Cart item ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item removed from cart"
 *     )
 * )
 */
Flight::route('DELETE /cart-items/@id', function ($id) {
    try {
        $res = Flight::cartItemService()->delete_cart_item($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
