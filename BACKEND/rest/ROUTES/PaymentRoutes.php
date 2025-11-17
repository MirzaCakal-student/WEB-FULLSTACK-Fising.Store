<?php
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(
 *   name="payments",
 *   description="Order payments"
 * )
 */

/**
 * @OA\Get(
 *   path="/payments",
 *   tags={"payments"},
 *   summary="Get all payments",
 *   @OA\Response(response=200, description="Payments list")
 * )
 */
Flight::route('GET /payments', function () {
    Flight::json([
        'success' => true,
        'data'    => Flight::paymentService()->get_all()
    ]);
});

/**
 * @OA\Get(
 *   path="/payments/{id}",
 *   tags={"payments"},
 *   summary="Get payment by ID",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Payment found")
 * )
 */
Flight::route('GET /payments/@id', function ($id) {
    $p = Flight::paymentService()->get_by_id($id);
    if ($p) {
        Flight::json(['success' => true, 'data' => $p]);
    } else {
        Flight::json(['success' => false, 'message' => 'Payment not found'], 404);
    }
});

/**
 * @OA\Post(
 *   path="/payments",
 *   tags={"payments"},
 *   summary="Create payment record",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"order_id","amount","method"},
 *       @OA\Property(property="order_id", type="integer", example=1),
 *       @OA\Property(property="amount", type="number", format="float", example=120.50),
 *       @OA\Property(property="method", type="string", example="card"),
 *       @OA\Property(property="status", type="string", example="paid")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Payment created")
 * )
 */
Flight::route('POST /payments', function () {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::paymentService()->add($data)
    ]);
});

/**
 * @OA\Put(
 *   path="/payments/{id}",
 *   tags={"payments"},
 *   summary="Update payment",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       @OA\Property(property="status", type="string"),
 *       @OA\Property(property="amount", type="number", format="float")
 *     )
 *   ),
 *   @OA\Response(response=200, description="Payment updated")
 * )
 */
Flight::route('PUT /payments/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json([
        'success' => true,
        'data'    => Flight::paymentService()->update($data, $id, 'payment_id')
    ]);
});

/**
 * @OA\Delete(
 *   path="/payments/{id}",
 *   tags={"payments"},
 *   summary="Delete payment record",
 *   @OA\Parameter(name="id", in="path", required=true,
 *      @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(response=200, description="Payment deleted")
 * )
 */
Flight::route('DELETE /payments/@id', function ($id) {
    Flight::paymentService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Payment deleted']);
});
