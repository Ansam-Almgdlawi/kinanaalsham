<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Repositories\OpportunityApplicationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail; // لاستخدام الإشعارات بالبريد الإلكتروني

class OpportunityApplicationService
{
    protected OpportunityApplicationRepositoryInterface $applicationRepository;

    public function __construct(OpportunityApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * تمكين المستخدم من التقديم على فرصة.
     *
     * @param array $data البيانات من الطلب (opportunity_id, cover_letter).
     * @param int $applicantUserId معرف المستخدم المتقدم.
     * @return OpportunityApplication
     */
    public function applyForOpportunity(array $data, int $applicantUserId): OpportunityApplication
    {
        $opportunityId = $data['opportunity_id'] ?? null;

        $opportunity = Opportunity::findOrFail($opportunityId);

        if ($opportunity->status !== 'open') {
            throw ValidationException::withMessages([
                'opportunity_id' => ['هذه الفرصة لم تعد متاحة.'],
            ]);
        }

        $alreadyApplied = $this->applicationRepository
            ->existsForUserAndOpportunity($applicantUserId, $opportunityId);

        if ($alreadyApplied) {
            throw ValidationException::withMessages([
                'opportunity_id' => ['لقد تقدمت مسبقًا لهذه الفرصة.'],
            ]);
        }

        // ✅ إدراج الطلب
        $data['applicant_user_id'] = $applicantUserId;
        $data['application_date'] = now();
        $data['status'] = 'pending_review';

        return $this->applicationRepository->create($data);
    }

    /**
     * جلب جميع طلبات التقديم المعلقة للمراجعة (للأدمن).
     *
     * @return Collection<OpportunityApplication>
     */
    public function getPendingApplications(): Collection
    {
        return $this->applicationRepository->getApplicationsByStatus('pending_review')->load(['opportunity', 'applicant']);
    }

    /**
     * تغيير حالة طلب التقديم.
     *
     * @param int $applicationId معرف الطلب.
     * @param string $newStatus الحالة الجديدة.
     * @param int $adminId معرف الأدمن الذي قام بالمراجعة.
     * @return OpportunityApplication
     * @throws ValidationException إذا لم يتم العثور على الطلب.
     */
    public function changeApplicationStatus(int $applicationId, string $newStatus, int $adminId): OpportunityApplication
    {
        $application = $this->applicationRepository->findById($applicationId);

        if (!$application) {
            throw ValidationException::withMessages([
                'application_id' => ['طلب التقديم المطلوب غير موجود.'],
            ]);
        }

        $application->status = $newStatus;
        $application->reviewed_by_user_id = $adminId;
        $application->save();
        // $this->sendApplicationStatusNotification($application);

        return $application;
    }

    /**
     * جلب جميع طلبات التقديم لمستخدم معين.
     *
     * @param int $userId معرف المستخدم.
     * @return Collection<OpportunityApplication>
     */
    public function getUserApplications(int $userId): Collection
    {
        return $this->applicationRepository->getUserApplications($userId);
    }


}
