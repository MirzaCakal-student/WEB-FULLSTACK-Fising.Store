<?php
 //finished
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(
 *   name="addresses",
 *   description="User shipping/billing addresses"
 * )
 */

/**
 * @OA\Get(
 *   path="/addresses",
 *   tags={"addresses"},
 *   summary="Get all addresses",
 *   @OA\Response(response=200, description="List of addresses")
 * )
 */
Flight::route('GET /addresses', function () {
    Flight::json([
        'success' => true,
        'data'    => Flight::addressService()->get_all()
    ]);
});

/**
 * @OA\Get(
 *   path="/addresses/{id}",
 *   tags={"addresses"},
 *   summary="Get address by ID",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Address found")
 * )
 */
Flight::route('GET /addresses/@id', function ($id) {
    $address = Flight::addressService()->get_by_id($id);
    if ($address) {
        Flight::json(['success' => true, 'data' => $address]);
    } else {
        Flight::json(['success' => false, 'message' => 'Address not found'], 404);
    }
});

/**
 * @OA\Post(
 *   path="/addresses",
 *   tags={"addresses"},
 *   summary="Create new address",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"user_id","street","city","country"},
 *       @OA\Property(property="user_id", type="integer", example=1),
 *       @OA\Property(property="street", type="string", example="Zmaja od Bosne 12"),
 *       @OA\Property(property="city", type="string", example="Sarajevo"),
 *       @OA\Property(property="zip", type="string", example="71000"),
 *       @OA\Property(property="country", type="string", example="Bosnia and Herzegovina")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Address created")
 * )
 */
Flight::route('POST /addresses', function () {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::addressService()->add($data)
    ]);
});

/**
 * @OA\Put(
 *   path="/addresses/{id}",
 *   tags={"addresses"},
 *   summary="Update address",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       @OA\Property(property="street", type="string"),
 *       @OA\Property(property="city", type="string"),
 *       @OA\Property(property="zip", type="string"),
 *       @OA\Property(property="country", type="string")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Address updated")
 * )
 */
Flight::route('PUT /addresses/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::addressService()->update($data, $id, 'address_id')
    ]);
});

/**
 * @OA\Delete(
 *   path="/addresses/{id}",
 *   tags={"addresses"},
 *   summary="Delete address",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Address deleted")
 * )
 */
Flight::route('DELETE /addresses/@id', function ($id) {
    Flight::addressService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Address deleted']);
});
