<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Http\Requests\Properties\StorePropertyRequest;
use App\Http\Requests\Properties\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Repositories\Property\PropertyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use OpenApi\Annotations as OA;

use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Tag(
 *     name="Properties",
 *     description="API endpoints for managing properties"
 * )
 */
class PropertyController extends BaseController
{
     /**
     * @OA\Schema(
     *     schema="Property",
     *     type="object",
     *     required={"id", "name", "description", "price", "address", "landlord_id"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Beautiful House"),
     *     @OA\Property(property="description", type="string", example="A beautiful 3-bedroom house"),
     *     @OA\Property(property="address", type="string", example="Koinange Street"),
     *     @OA\Property(property="city", type="text", format="float", example="Nairobi"),
     *     @OA\Property(property="state", type="string", example="Kenya"),
     *     @OA\Property(property="zip_code", type="string", example="00100"),
     *     @OA\Property(property="features", type="array", @OA\Items(type="string"), example={"pool", "garden"}),
     * )
     */
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository
    ) {
        // $this->middleware('role:admin,agent,landlord')->only(['index', 'show']);
        // $this->middleware('role:admin,agent,landlord')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/properties",
     *     summary="Get a list of properties",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of properties",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Property")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'properties_' . $request->user()->id;
        $perPage = $request->per_page ?? 15;

        $properties = Cache::remember(
            $cacheKey,
            now()->addHour(),
            function () use ($request, $perPage) {
                return  $request->user()->isAdmin() || $request->user()->isAgent()
                ? $this->propertyRepository->paginate($perPage)
                : $this->propertyRepository->forLandlord($request->user()->id);
            });
        Log::info('Properties fetched from cache', ['cacheKey' => $cacheKey]);

        return PropertyResource::collection($properties);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/properties",
     *     summary="Store a new property",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Property")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Property created successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Property"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['landlord_id'] = $request->user()->id;

        $property = $this->propertyRepository->create($data);

        // Invalidate cache
        Cache::forget('properties_' . $request->user()->id);

        return response()->json([
            'data' => new PropertyResource($property),
            'message' => 'Property created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/properties/{id}",
     *     summary="Get a single property by ID",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the property",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property details",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Property"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $property = $this->propertyRepository->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json([
            'data' => new PropertyResource($property)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/properties/{id}",
     *     summary="Update an existing property",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the property to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Property")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property updated successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Property"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UpdatePropertyRequest $request, int $id): JsonResponse
    {
        $property = $this->propertyRepository->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $this->propertyRepository->update($id, $request->validated());

        // Invalidate cache for landlord
        Cache::forget('properties_' . $property->landlord_id);

        return response()->json([
            'data' => new PropertyResource($property->fresh()),
            'message' => 'Property updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/properties/{id}",
     *     summary="Delete a property",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the property to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Property deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $property = $this->propertyRepository->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Invalidate cache before deletion
        Cache::forget('properties_' . $property->landlord_id);

        $this->propertyRepository->delete($id);

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);
    }
}

