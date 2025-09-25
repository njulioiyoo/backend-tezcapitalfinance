<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotorController extends Controller
{
    /**
     * Get all motors for selection
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Motor::where('is_active', true);
            
            // Filter by area if provided
            if ($request->has('area') && $request->area) {
                $query->where('area', $request->area);
            }
            
            // Filter by period if provided
            if ($request->has('period') && $request->period) {
                $query->where('period', $request->period);
            }
            
            // Search by name if provided
            if ($request->has('search') && $request->search) {
                $query->where('name', 'ilike', '%' . $request->search . '%');
            }
            
            $motors = $query->orderBy('name', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Motors retrieved successfully',
                'data' => $motors->map(function ($motor) {
                    return [
                        'id' => $motor->id,
                        'name' => $motor->name,
                        'price' => $motor->price,
                        'area' => $motor->area,
                        'period' => $motor->period,
                        'installment_plans' => $motor->installment_plans
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve motors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available areas
     */
    public function getAreas(): JsonResponse
    {
        try {
            $areas = Motor::where('is_active', true)
                ->distinct()
                ->pluck('area')
                ->filter()
                ->sort()
                ->values();
                
            return response()->json([
                'success' => true,
                'message' => 'Areas retrieved successfully',
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve areas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available periods
     */
    public function getPeriods(): JsonResponse
    {
        try {
            $periods = Motor::where('is_active', true)
                ->distinct()
                ->pluck('period')
                ->filter()
                ->sort()
                ->values();
                
            return response()->json([
                'success' => true,
                'message' => 'Periods retrieved successfully', 
                'data' => $periods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve periods',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate installment for a specific motor
     */
    public function calculateInstallment(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'motor_id' => 'required|exists:motors,id',
                'dp_amount' => 'required|numeric|min:0',
                'tenor_months' => 'required|integer|in:11,17,23,29,35'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $motor = Motor::findOrFail($request->motor_id);
            $dpAmount = (float) $request->dp_amount;
            $tenorMonths = $request->tenor_months;
            
            \Log::info('Motor calculation request:', [
                'motor_id' => $request->motor_id,
                'motor_name' => $motor->name,
                'motor_price' => $motor->price,
                'dp_amount' => $dpAmount,
                'tenor_months' => $tenorMonths
            ]);
            
            // Find exact matching installment plan by DP amount
            $installmentPlans = $motor->installment_plans;
            $bestMatch = null;
            
            \Log::info('Looking for matching installment plan:', [
                'requested_dp_amount' => $dpAmount,
                'available_plans' => collect($installmentPlans)->map(fn($p) => [
                    'dp_percent' => $p['dp_percent'],
                    'dp_amount' => $p['dp_amount'],
                    'available_tenors' => array_keys($p['installments'] ?? [])
                ])
            ]);
            
            foreach ($installmentPlans as $plan) {
                // Match by exact DP amount instead of percentage
                if ($plan['dp_amount'] == $dpAmount) {
                    $bestMatch = $plan;
                    break;
                }
            }
            
            \Log::info('Best matching plan found:', [
                'best_match_dp_percent' => $bestMatch['dp_percent'] ?? null,
                'best_match_dp_amount' => $bestMatch['dp_amount'] ?? null,
                'match_found' => $bestMatch !== null,
                'available_tenors' => array_keys($bestMatch['installments'] ?? [])
            ]);
            
            if (!$bestMatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'No suitable installment plan found'
                ], 404);
            }
            
            // Get installment amount for the requested tenor
            $tenorKey = $tenorMonths . '_months';
            $monthlyInstallment = null;
            
            if (isset($bestMatch['installments'][$tenorKey])) {
                $monthlyInstallment = $bestMatch['installments'][$tenorKey];
            }
            
            if (!$monthlyInstallment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Installment plan not available for ' . $tenorMonths . ' months tenor'
                ], 404);
            }
            
            // Calculate totals
            $totalInstallment = $monthlyInstallment * $tenorMonths;
            $totalPayment = $dpAmount + $totalInstallment;
            $totalInterest = $totalPayment - $motor->price;
            
            return response()->json([
                'success' => true,
                'message' => 'Installment calculated successfully',
                'data' => [
                    'motor' => [
                        'id' => $motor->id,
                        'name' => $motor->name,
                        'price' => $motor->price,
                        'area' => $motor->area,
                        'period' => $motor->period
                    ],
                    'calculation' => [
                        'motor_price' => $motor->price,
                        'dp_amount' => $dpAmount,
                        'dp_percent' => $bestMatch['dp_percent'],
                        'tenor_months' => $tenorMonths,
                        'monthly_installment' => $monthlyInstallment,
                        'total_installment' => $totalInstallment,
                        'total_payment' => $totalPayment,
                        'total_interest' => $totalInterest,
                        'matched_plan' => [
                            'dp_percent' => $bestMatch['dp_percent'],
                            'dp_amount' => $bestMatch['dp_amount']
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate installment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get installment options for a specific motor
     */
    public function getInstallmentOptions(Request $request, $motorId): JsonResponse
    {
        try {
            $motor = Motor::findOrFail($motorId);
            
            $tenors = [11, 17, 23, 29, 35];
            $installmentPlans = $motor->installment_plans;
            
            $options = [];
            
            foreach ($installmentPlans as $plan) {
                $planData = [
                    'dp_percent' => $plan['dp_percent'],
                    'dp_amount' => $plan['dp_amount'],
                    'installments' => []
                ];
                
                foreach ($tenors as $tenor) {
                    $tenorKey = $tenor . '_months';
                    if (isset($plan['installments'][$tenorKey])) {
                        $planData['installments'][] = [
                            'tenor_months' => $tenor,
                            'monthly_amount' => $plan['installments'][$tenorKey],
                            'total_installment' => $plan['installments'][$tenorKey] * $tenor,
                            'total_payment' => $plan['dp_amount'] + ($plan['installments'][$tenorKey] * $tenor)
                        ];
                    }
                }
                
                $options[] = $planData;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Installment options retrieved successfully',
                'data' => [
                    'motor' => [
                        'id' => $motor->id,
                        'name' => $motor->name,
                        'price' => $motor->price,
                        'area' => $motor->area,
                        'period' => $motor->period
                    ],
                    'installment_options' => $options
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve installment options',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search motors by name, area, or price range
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Motor::where('is_active', true);
            
            // Search by name
            if ($request->has('name') && $request->name) {
                $query->where('name', 'ilike', '%' . $request->name . '%');
            }
            
            // Filter by area
            if ($request->has('area') && $request->area) {
                $query->where('area', $request->area);
            }
            
            // Filter by price range
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $motors = $query->orderBy('name', 'asc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Motors search completed successfully',
                'data' => $motors->items(),
                'pagination' => [
                    'current_page' => $motors->currentPage(),
                    'per_page' => $motors->perPage(),
                    'total' => $motors->total(),
                    'last_page' => $motors->lastPage(),
                    'from' => $motors->firstItem(),
                    'to' => $motors->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search motors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}