<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Repositories\Payment\PaymentRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Requests\Payments\UpdatePaymentRequest;
use App\Jobs\SendPaymentReceipt;


/**
 * @OA\Tag(
 *     name="Payments",
 *     description="API endpoints for managing payments"
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Payment",
     *     type="object",
     *     required={"lease_id", "amount", "payment_date", "payment_method"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="lease_id", type="integer", example=1),
     *     @OA\Property(property="amount", type="number", format="float", example=200),
     *     @OA\Property(property="payment_date", type="string", format="date", example="2025-05-15"),
     *     @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *     @OA\Property(property="transaction_reference", type="string", example="TXN12345"),
     *     @OA\Property(property="status", type="string", example="completed"),
     *     @OA\Property(property="notes", type="string", example="Payment for May rent"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-15T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-15T12:00:00Z")
     * )
     */
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     summary="Get a list of payments",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of payments",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Payment"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $payments = $this->paymentRepository->paginate();
        return PaymentResource::collection($payments);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     summary="Record a new payment",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"lease_id", "amount", "payment_date", "payment_method"},
     *             @OA\Property(property="lease_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=200),
     *             @OA\Property(property="payment_date", type="string", format="date", example="2025-05-15"),
     *             @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *             @OA\Property(property="transaction_reference", type="string", example="TXN12345"),
     *             @OA\Property(property="status", type="string", example="completed"),
     *             @OA\Property(property="notes", type="string", example="Payment for May rent")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment recorded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
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
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $payment = $this->paymentRepository->create($data);

        // Dispatch payment receipt job
        dispatch((new SendPaymentReceipt($payment))->delay(now()->addSeconds(10))); // Optional delay

        return response()->json([
            'data' => new PaymentResource($payment),
            'message' => 'Payment recorded successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/{id}",
     *     summary="Get a specific payment",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json([
            'data' => new PaymentResource($payment)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/payments/{id}",
     *     summary="Update payment details",
     *     tags={"Payments"},
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
     *             @OA\Property(property="amount", type="number", format="float", example=250),
     *             @OA\Property(property="payment_method", type="string", example="Debit Card"),
     *             @OA\Property(property="status", type="string", example="completed"),
     *             @OA\Property(property="notes", type="string", example="Updated payment details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $this->paymentRepository->update($id, $request->validated());

        return response()->json([
            'data' => new PaymentResource($payment->fresh()),
            'message' => 'Payment updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/payments/{id}",
     *     summary="Delete a payment",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $this->paymentRepository->delete($id);

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
