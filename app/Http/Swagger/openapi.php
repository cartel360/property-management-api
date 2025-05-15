<?php

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Property Management API",
 *         description="API for property management system including authentication, properties, tenants, leases and payments management",
 *         termsOfService="https://example.com/terms",
 *         @OA\Contact(
 *             email="support@propertymanagement.com",
 *             name="API Support"
 *         ),
 *         @OA\License(
 *             name="MIT",
 *             url="https://opensource.org/licenses/MIT"
 *         )
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_HOST,
 *         description="Main API Server"
 *     ),
 *     @OA\Server(
 *         url="https://api.propertymanagement.com",
 *         description="Production Server"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT",
 *         description="Use JWT token for authentication. Register to get token."
 *     ),
 *     @OA\Tag(
 *         name="Authentication",
 *         description="User registration, login and logout endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Properties",
 *         description="Property management endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Tenants",
 *         description="Tenant management endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Leases",
 *         description="Lease agreement endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Payments",
 *         description="Payment processing endpoints"
 *     ),
 *     /**
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     @OA\Property(property="access_token", type="string"),
 *     @OA\Property(property="token_type", type="string", example="Bearer"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="role", type="string")
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="role", type="string"),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true)
 * ),
 * /**
 * @OA\Schema(
 *     schema="Property",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Beautiful House"),
 *     @OA\Property(property="description", type="string", example="A beautiful 3-bedroom house"),
 *     @OA\Property(property="price", type="number", format="float", example=250000.00),
 *     @OA\Property(property="address", type="string", example="1234 Elm Street"),
 *     @OA\Property(property="landlord_id", type="integer", example=1)
 * )
 * )
 */
