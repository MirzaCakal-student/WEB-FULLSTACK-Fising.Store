<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Fishing Planet API",
 *     version="1.0.0",
 *     description="OpenAPI documentation for the Fishing Planet backend (products, cart, wishlist, orders, users, payments, etc.)."
 * )
 *
 * @OA\Server(
 *     url=BASE_URL,
 *     description="Local development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */
