<?php
use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/products",
 *     tags={"products"},
 *     summary="Get all products",
 *     @OA\Response(
 *         response=200,
 *         description="List of all products"
 *     )
 * )
 */
Flight::route('GET /products', function () {
    try {
        $products = Flight::productService()->get_products();
        Flight::json(['success' => true, 'data' => $products]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Get product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Single product"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
Flight::route('GET /products/@id', function ($id) {
    try {
        $product = Flight::productService()->get_product_by_id($id);
        Flight::json(['success' => true, 'data' => $product]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/products",
 *     tags={"products"},
 *     summary="Create new product",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","category","price"},
 *             @OA\Property(property="name", type="string", example="Spinning Rod X"),
 *             @OA\Property(property="category", type="string", example="Rods"),
 *             @OA\Property(property="price", type="number", example=89.99),
 *             @OA\Property(property="stock_quantity", type="integer", example=20),
 *             @OA\Property(property="image_url", type="string", example="https://..."),
 *             @OA\Property(property="description", type="string", example="Nice rod...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product created"
 *     )
 * )
 */
Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    try {
        $created = Flight::productService()->add_product($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Update product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="category", type="string"),
 *             @OA\Property(property="price", type="number"),
 *             @OA\Property(property="stock_quantity", type="integer"),
 *             @OA\Property(property="image_url", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated"
 *     )
 * )
 */
Flight::route('PUT /products/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::productService()->update_product($id, $data);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/products/{id}",
 *     tags={"products"},
 *     summary="Delete product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted"
 *     )
 * )
 */
Flight::route('DELETE /products/@id', function ($id) {
    try {
        $res = Flight::productService()->delete_product($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
