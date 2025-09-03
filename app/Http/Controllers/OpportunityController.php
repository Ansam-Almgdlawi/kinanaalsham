<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpportunityRequest;
use App\Http\Requests\UpdateOpportunityStatusRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Models\VolunteerDetail;
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
    public function recommend(Request $request)
    {
        $volunteer = Auth::user();

        if (!$volunteer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // 2. نفس المنطق السابق
        $opportunities = Opportunity::where('status', 'open')->get();
        $recommendations = [];

        foreach ($opportunities as $opportunity) {
            $score = 0;

            // 1. تطابق الاهتمامات مع المجال
            if ($volunteer->interests === $opportunity->category) {
                $score += 50;
            }

            // 2. مقارنة المهارات
            $volunteerSkills = array_map('trim', explode(',', strtolower($volunteer->skills ?? '')));
            $opportunitySkills = array_map('trim', explode(',', strtolower($opportunity->skills ?? '')));
            $commonSkills = array_intersect($volunteerSkills, $opportunitySkills);
            $score += count($commonSkills) * 10;

            // 3. نوع التطوع (عن بعد أو ميداني)
            if (($volunteer->volunteering_type_preference === 'remote' && $opportunity->is_remote) ||
                ($volunteer->volunteering_type_preference === 'on_site' && !$opportunity->is_remote)) {
                $score += 5;
            }

            $recommendations[] = [
                'opportunity' => $opportunity,
                'score' => $score,
                'matched_skills' => array_values($commonSkills),
            ];
        }

        // ترتيب حسب أعلى Score
        usort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);

        return response()->json($recommendations);
    }


}
