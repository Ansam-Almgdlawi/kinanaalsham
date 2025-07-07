<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CreateApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Http\Resources\ApplicationResource;
use App\Services\OpportunityApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class OpportunityApplicationController extends Controller
{
    protected OpportunityApplicationService $applicationService;

    public function __construct(OpportunityApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * تمكين المستخدم من التقديم على فرصة.
     *
     * @param CreateApplicationRequest $request
     * @return JsonResponse
     */
    public function apply(CreateApplicationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $applicantId = Auth::id(); // معرف المستخدم الحالي هو المتقدم

        try {
            $application = $this->applicationService->applyForOpportunity($validatedData, $applicantId);
            return response()->json(new ApplicationResource($application), 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل التقديم على الفرصة.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض جميع طلبات التقديم لمستخدم معين.
     *
     * @return AnonymousResourceCollection
     */
    public function indexUserApplications(): AnonymousResourceCollection
    {
        $userId = Auth::id();
        $applications = $this->applicationService->getUserApplications($userId);

        return ApplicationResource::collection($applications);
    }

    /**
     * عرض جميع طلبات التقديم المعلقة للمراجعة (للأدمن).
     *
     * @return AnonymousResourceCollection
     */
    public function indexPendingApplications(): AnonymousResourceCollection
    {
        // يجب أن يكون المستخدم أدمن، وهذا يتم التحقق منه في المسار أو في الطلب
        $applications = $this->applicationService->getPendingApplications();

        return ApplicationResource::collection($applications);
    }

    /**
     * تغيير حالة طلب التقديم (للأدمن).
     *
     * @param UpdateApplicationStatusRequest $request
     * @param int $id معرف طلب التقديم.
     * @return JsonResponse
     */
    public function updateStatus(UpdateApplicationStatusRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $newStatus = $validatedData['status'];
        $reviewNotes = $validatedData['review_notes'] ?? null;
        $adminId = Auth::id();

        try {
            $application = $this->applicationService->changeApplicationStatus($id, $newStatus, $adminId, $reviewNotes);
            return response()->json(new ApplicationResource($application), 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث حالة الطلب.',
                'errors' => $e->errors(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ غير متوقع.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
