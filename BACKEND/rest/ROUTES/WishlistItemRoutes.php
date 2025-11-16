<?php
use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/wishlist-items",
 *     tags={"wishlist"},
 *     summary="Get wishlist items (optionally filtered by user)",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer"),
 *         description="Filter wishlist by user ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Wishlist items list"
 *     )
 * )
 */
Flight::route('GET /wishlist-items', function () {
    $user_id = Flight::request()->query['user_id'] ?? null;
    try {
        $items = Flight::wishlistItemService()->get_wishlist_items($user_id);
        Flight::json(['success' => true, 'data' => $items]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/wishlist-items",
 *     tags={"wishlist"},
 *     summary="Add item to wishlist",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","product_id"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item added to wishlist"
 *     )
 * )
 */
Flight::route('POST /wishlist-items', function () {
    $data = Flight::request()->data->getData();
    try {
        $created = Flight::wishlistItemService()->add_wishlist_item($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/wishlist-items/{id}",
 *     tags={"wishlist"},
 *     summary="Delete wishlist item",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Wishlist item ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item removed from wishlist"
 *     )
 * )
 */
Flight::route('DELETE /wishlist-items/@id', function ($id) {
    try {
        $res = Flight::wishlistItemService()->delete_wishlist_item($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
