<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Repositories\OpportunityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
class OpportunityService
{
    protected OpportunityRepositoryInterface $opportunityRepository;

    /**
     * حقن التبعية لمستودع الفرص.
     *
     * @param OpportunityRepositoryInterface $opportunityRepository
     */
    public function __construct(OpportunityRepositoryInterface $opportunityRepository)
    {
        $this->opportunityRepository = $opportunityRepository;
    }

    /**
     * إنشاء فرصة جديدة.
     *
     * @param array $data البيانات من الطلب (بعد التحقق).
     * @param int $adminId معرف المستخدم الأدمن الذي ينشئ الفرصة.
     * @return Opportunity
     */
    public function createOpportunity(array $data, int $adminId): Opportunity
    {

        $data['posted_by_user_id'] = $adminId;

        $data['status'] = $data['status'] ?? 'open';
        $data['is_remote'] = $data['is_remote'] ?? false;
        $opportunity = $this->opportunityRepository->create($data);

        return $opportunity;
    }
    public function getAvailableJobOpportunities(): Collection
    {
        // هنا يمكنك إضافة أي منطق عمل إضافي قبل جلب الفرص.
        // مثلاً، التحقق من صلاحيات المستخدم إذا كان هذا API يتطلب ذلك.

        return $this->opportunityRepository->getOpportunitiesByTypeAndStatus('job', 'open');
    }

    /**
     *
     * @return Collection<Opportunity>
     */
    public function getAvailableVolunteeringOpportunities(): Collection
    {
        return $this->opportunityRepository->getOpportunitiesByTypeAndStatus('volunteering', 'open');
    }
    public function changeOpportunityStatus(int $opportunityId, string $newStatus, int $adminId): Opportunity
    {
        // يمكنك هنا إضافة منطق عمل إضافي، مثل:
        // - تسجيل من قام بتغيير الحالة.
        // - إرسال إشعارات عند تغيير الحالة.
        // - التحقق من أن الأدمن لديه الصلاحية لتغيير حالة هذه الفرصة بالذات (إذا كان هناك مستويات صلاحية أدمن مختلفة).

        $opportunity = $this->opportunityRepository->updateStatus($opportunityId, $newStatus);

        if (!$opportunity) {
            // يمكن رمي استثناء مخصص هنا أو استخدام ValidationException
            throw ValidationException::withMessages([
                'opportunity_id' => ['الفرصة المطلوبة غير موجودة.'],
            ]);
        }

        return $opportunity;
    }
}
