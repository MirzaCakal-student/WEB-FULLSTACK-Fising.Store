<?php
 //finished
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(
 *   name="inventory",
 *   description="Product stock / inventory"
 * )
 */

/**
 * @OA\Get(
 *   path="/inventory",
 *   tags={"inventory"},
 *   summary="Get all inventory records",
 *   @OA\Response(response=200, description="Inventory list")
 * )
 */
Flight::route('GET /inventory', function () {
    Flight::json([
        'success' => true,
        'data'    => Flight::inventoryService()->get_all()
    ]);
});

/**
 * @OA\Get(
 *   path="/inventory/{id}",
 *   tags={"inventory"},
 *   summary="Get inventory record by ID",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Inventory record")
 * )
 */
Flight::route('GET /inventory/@id', function ($id) {
    $inv = Flight::inventoryService()->get_by_id($id);
    if ($inv) {
        Flight::json(['success' => true, 'data' => $inv]);
    } else {
        Flight::json(['success' => false, 'message' => 'Inventory record not found'], 404);
    }
});

/**
 * @OA\Post(
 *   path="/inventory",
 *   tags={"inventory"},
 *   summary="Create inventory record",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"product_id","quantity"},
 *       @OA\Property(property="product_id", type="integer", example=1),
 *       @OA\Property(property="quantity", type="integer", example=100)
 *     )
 *   ),
 *   @OA\Response(response=200, description="Inventory created")
 * )
 */
Flight::route('POST /inventory', function () {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::inventoryService()->add($data)
    ]);
});

/**
 * @OA\Put(
 *   path="/inventory/{id}",
 *   tags={"inventory"},
 *   summary="Update inventory record",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       @OA\Property(property="quantity", type="integer")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Inventory updated")
 * )
 */
Flight::route('PUT /inventory/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::inventoryService()->update($data, $id, 'inventory_id')
    ]);
});

/**
 * @OA\Delete(
 *   path="/inventory/{id}",
 *   tags={"inventory"},
 *   summary="Delete inventory record",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Inventory deleted")
 * )
 */
Flight::route('DELETE /inventory/@id', function ($id) {
    Flight::inventoryService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Inventory record deleted']);
});
