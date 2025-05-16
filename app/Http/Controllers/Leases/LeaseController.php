<?php

namespace App\Http\Controllers\Leases;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;
use App\Repositories\Lease\LeaseRepositoryInterface;
use App\Http\Resources\LeaseResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Leases\StoreLeaseRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Leases\UpdateLeaseRequest;

/**
 * @OA\Tag(
 *     name="Leases",
 *     description="API endpoints for managing leases"
 * )
 */
class LeaseController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Lease",
     *     type="object",
     *     required={"unit_id", "tenant_id", "start_date", "end_date", "monthly_rent", "security_deposit", "status"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="unit_id", type="integer", example=1),
     *     @OA\Property(property="tenant_id", type="integer", example=1),
     *     @OA\Property(property="start_date", type="string", format="date", example="2025-06-01"),
     *     @OA\Property(property="end_date", type="string", format="date", example="2026-06-01"),
     *     @OA\Property(property="monthly_rent", type="number", format="float", example=1200.00),
     *     @OA\Property(property="security_deposit", type="number", format="float", example=2400.00),
     *     @OA\Property(property="status", type="string", example="active"),
     *     @OA\Property(property="terms", type="string", example="Standard lease agreement terms"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-15T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-15T12:00:00Z")
     * )
     */
    public function __construct(
        private LeaseRepositoryInterface $leaseRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/leases",
     *     summary="Get a list of leases",
     *     tags={"Leases"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of leases",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Lease")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $leases = $this->leaseRepository->paginate();
        return LeaseResource::collection($leases);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/leases",
     *     summary="Create a new lease",
     *     tags={"Leases"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"unit_id", "tenant_id", "start_date", "end_date", "monthly_rent", "security_deposit", "status"},
     *             @OA\Property(property="unit_id", type="integer", example=1),
     *             @OA\Property(property="tenant_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-06-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2026-06-01"),
     *             @OA\Property(property="monthly_rent", type="number", format="float", example=1200.00),
     *             @OA\Property(property="security_deposit", type="number", format="float", example=2400.00),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="terms", type="string", example="Standard lease agreement terms")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lease created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Lease")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unit already leased"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(StoreLeaseRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Check if unit is available
        $activeLease = $this->leaseRepository->forUnit($data['unit_id'])
            ->where('status', 'active')
            ->first();

        if ($activeLease) {
            return response()->json([
                'message' => 'Unit is already leased'
            ], 422);
        }

        $lease = $this->leaseRepository->create($data);

        // Update the unit status to 'occupied'
        $unit = $lease->unit;
        $unit->update(['status' => 'occupied']);

        return response()->json([
            'data' => new LeaseResource($lease),
            'message' => 'Lease created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/leases/{id}",
     *     summary="Get a lease by ID",
     *     tags={"Leases"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lease data",
     *         @OA\JsonContent(ref="#/components/schemas/Lease")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lease not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $lease = $this->leaseRepository->find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        return response()->json([
            'data' => new LeaseResource($lease)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/leases/{id}",
     *     summary="Update a lease",
     *     tags={"Leases"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"unit_id", "tenant_id", "start_date", "end_date", "monthly_rent", "security_deposit", "status"},
     *             @OA\Property(property="unit_id", type="integer", example=1),
     *             @OA\Property(property="tenant_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-06-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2026-06-01"),
     *             @OA\Property(property="monthly_rent", type="number", format="float", example=1200.00),
     *             @OA\Property(property="security_deposit", type="number", format="float", example=2400.00),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="terms", type="string", example="Standard lease agreement terms")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lease updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Lease")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lease not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UpdateLeaseRequest $request, int $id): JsonResponse
    {
        $lease = $this->leaseRepository->find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $this->leaseRepository->update($id, $request->validated());

        return response()->json([
            'data' => new LeaseResource($lease->fresh()),
            'message' => 'Lease updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/leases/{id}",
     *     summary="Delete a lease",
     *     tags={"Leases"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lease deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lease not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $lease = $this->leaseRepository->find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $this->leaseRepository->delete($id);

        return response()->json([
            'message' => 'Lease deleted successfully'
        ]);
    }
}
