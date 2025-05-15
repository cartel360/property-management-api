<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Tenant\TenantRepositoryInterface;
use App\Http\Resources\TenantResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Tenants\StoreTenantRequest;
use App\Http\Requests\Tenants\UpdateTenantRequest;

/**
 * @OA\Tag(
 *     name="Tenants",
 *     description="API endpoints for managing tenants"
 * )
 */
class TenantController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Tenant",
     *     type="object",
     *     required={"name", "email", "phone", "address", "date_of_birth"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *     @OA\Property(property="phone", type="string", example="123-456-7890"),
     *     @OA\Property(property="address", type="string", example="123 Main St"),
     *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *     @OA\Property(property="emergency_contact_name", type="string", example="Jane Doe"),
     *     @OA\Property(property="emergency_contact_phone", type="string", example="987-654-3210"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-15T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-15T12:00:00Z")
     * )
     */
    public function __construct(
        private TenantRepositoryInterface $tenantRepository
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tenants",
     *     summary="Get a list of tenants",
     *     tags={"Tenants"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search tenants by name or other attributes",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of tenants",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tenant")
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
        $tenants = $request->has('search')
            ? $this->tenantRepository->search($request->search)
            : $this->tenantRepository->paginate();

        return TenantResource::collection($tenants);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tenants",
     *     summary="Create a new tenant",
     *     tags={"Tenants"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone", "address", "date_of_birth"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="emergency_contact_name", type="string", example="Jane Doe"),
     *             @OA\Property(property="emergency_contact_phone", type="string", example="987-654-3210")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tenant created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Tenant")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantRepository->create($request->validated());

        return response()->json([
            'data' => new TenantResource($tenant),
            'message' => 'Tenant created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tenants/{id}",
     *     summary="Get a tenant by ID",
     *     tags={"Tenants"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant data",
     *         @OA\JsonContent(ref="#/components/schemas/Tenant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $tenant = $this->tenantRepository->find($id);

        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        return response()->json([
            'data' => new TenantResource($tenant)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tenants/{id}",
     *     summary="Update a tenant",
     *     tags={"Tenants"},
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
     *             required={"name", "email", "phone", "address", "date_of_birth"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="emergency_contact_name", type="string", example="Jane Doe"),
     *             @OA\Property(property="emergency_contact_phone", type="string", example="987-654-3210")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Tenant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantRepository->find($id);

        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $this->tenantRepository->update($id, $request->validated());

        return response()->json([
            'data' => new TenantResource($tenant->fresh()),
            'message' => 'Tenant updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tenants/{id}",
     *     summary="Delete a tenant",
     *     tags={"Tenants"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $tenant = $this->tenantRepository->find($id);

        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $this->tenantRepository->delete($id);

        return response()->json([
            'message' => 'Tenant deleted successfully'
        ]);
    }
}
