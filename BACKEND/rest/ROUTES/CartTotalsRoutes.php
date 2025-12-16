<?php
 //finished
use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/cart-items",
 *     tags={"cart"},
 *     summary="Get cart items for a user",
 *     @OA\Parameter(
 *         name="user_id", in="query", required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Cart items for user")
 * )
 */
Flight::route('GET /cart-items', function () {
    $user_id = Flight::request()->query['user_id'] ?? null;
    Flight::json(['success' => true, 'data' => Flight::cartItemService()->get_cart_items($user_id)]);
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
 *     @OA\Response(response=200, description="Item added to cart")
 * )
 */
Flight::route('POST /cart-items', function () {
    $data = Flight::request()->data->getData();
    Flight::json(['success' => true, 'data' => Flight::cartItemService()->add_cart_item($data)]);
});

/**
 * @OA\Put(
 *     path="/cart-items/{id}",
 *     tags={"cart"},
 *     summary="Update cart item quantity",
 *     @OA\Parameter(name="id", in="path", required=true,
 *       @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Cart item updated")
 * )
 */
Flight::route('PUT /cart-items/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(['success' => true, 'data' => Flight::cartItemService()->update_cart_item($id, $data)]);
});

/**
 * @OA\Delete(
 *     path="/cart-items/{id}",
 *     tags={"cart"},
 *     summary="Remove cart item",
 *     @OA\Parameter(name="id", in="path", required=true,
 *       @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(response=200, description="Cart item deleted")
 * )
 */
Flight::route('DELETE /cart-items/@id', function ($id) {
    Flight::json(['success' => true, 'data' => Flight::cartItemService()->delete_cart_item($id)]);
});