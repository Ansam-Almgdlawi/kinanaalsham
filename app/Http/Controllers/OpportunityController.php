<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpportunityRequest;
use App\Http\Requests\UpdateOpportunityStatusRequest;
use App\Http\Resources\OpportunityResource;
use App\Services\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OpportunityController extends Controller
{
    protected OpportunityService $opportunityService;

    /**
     * حقن التبعية لخدمة الفرص.
     *
     * @param OpportunityService $opportunityService
     */
    public function __construct(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    /**
     * إنشاء فرصة عمل أو تطوع جديدة.
     *
     * @param CreateOpportunityRequest $request
     * @return JsonResponse
     */
    public function store(CreateOpportunityRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $adminId = Auth::id();

        $opportunity = $this->opportunityService->createOpportunity($validatedData, $adminId);

        return response()->json(new OpportunityResource($opportunity), 201); // 201 Created
    }
    public function indexJobs(): AnonymousResourceCollection
    {
        $jobOpportunities = $this->opportunityService->getAvailableJobOpportunities();

        return OpportunityResource::collection($jobOpportunities);
    }

    public function indexVolunteering(): AnonymousResourceCollection
    {
        $volunteeringOpportunities = $this->opportunityService->getAvailableVolunteeringOpportunities();

        return OpportunityResource::collection($volunteeringOpportunities);
    }
    public function updateStatus(UpdateOpportunityStatusRequest $request, int $id): JsonResponse
    {
        try {
            $newStatus = $request->validated('status');
            $adminId = Auth::id();

            $opportunity = $this->opportunityService->changeOpportunityStatus($id, $newStatus, $adminId);

            return response()->json(new OpportunityResource($opportunity), 200); // 200 OK
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث حالة الفرصة.',
                'errors' => $e->errors(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ غير متوقع.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
}
