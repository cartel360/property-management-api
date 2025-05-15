<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Http\Requests\Properties\StoreUnitRequest;
use App\Http\Requests\Properties\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Repositories\Unit\UnitRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Unit",
     *     type="object",
     *     required={"unit_number", "rent_amount", "size", "bedrooms", "bathrooms", "features", "status"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="property_id", type="integer", example=1),
     *     @OA\Property(property="unit_number", type="string", example="A101"),
     *     @OA\Property(property="rent_amount", type="number", format="float", example=1200.00),
     *     @OA\Property(property="size", type="number", example=50),
     *     @OA\Property(property="bedrooms", type="integer", example=2),
     *     @OA\Property(property="bathrooms", type="integer", example=1),
     *     @OA\Property(property="features", type="array", items=@OA\Items(type="string"), example={"balcony", "parking"}),
     *     @OA\Property(property="status", type="string", example="vacant"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-15T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-15T12:00:00Z")
     * )
     */

    public function __construct(
        private UnitRepositoryInterface $unitRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/properties/{propertyId}/units",
     *     summary="Get all units for a property",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of units",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Unit")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     )
     * )
     */
    public function index(int $propertyId): AnonymousResourceCollection
    {
        $units = $this->unitRepository->forProperty($propertyId);
        return UnitResource::collection($units);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/properties/{propertyId}/units",
     *     summary="Create a new unit for a property",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(ref="#/components/schemas/Unit")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Unit created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreUnitRequest $request, int $propertyId): JsonResponse
    {
        $data = $request->validated();
        $data['property_id'] = $propertyId;

        $unit = $this->unitRepository->create($data);

        return response()->json([
            'data' => new UnitResource($unit),
            'message' => 'Unit created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/units/{id}",
     *     summary="Get a specific unit",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unit details",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unit not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $unit = $this->unitRepository->find($id);

        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        return response()->json([
            'data' => new UnitResource($unit)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/units/{id}",
     *     summary="Update an existing unit",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(ref="#/components/schemas/Unit")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unit updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Unit")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unit not found"
     *     )
     * )
     */
    public function update(UpdateUnitRequest $request, int $id): JsonResponse
    {
        $unit = $this->unitRepository->find($id);

        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        $this->unitRepository->update($id, $request->validated());

        return response()->json([
            'data' => new UnitResource($unit->fresh()),
            'message' => 'Unit updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/units/{id}",
     *     summary="Delete a unit",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unit deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unit not found"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $unit = $this->unitRepository->find($id);

        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        $this->unitRepository->delete($id);

        return response()->json([
            'message' => 'Unit deleted successfully'
        ]);
    }
}
