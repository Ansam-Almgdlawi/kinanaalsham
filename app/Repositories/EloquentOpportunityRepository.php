<?php

namespace App\Repositories;

use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Collection;

class EloquentOpportunityRepository implements OpportunityRepositoryInterface
{
    /**
     * إنشاء فرصة جديدة في قاعدة البيانات.
     *
     * @param array $data
     * @return Opportunity
     */
    public function create(array $data): Opportunity
    {
        return Opportunity::create($data);
    }
    public function getOpportunitiesByTypeAndStatus(string $type, string $status): Collection
    {
        return Opportunity::where('type', $type)
            ->where('status', $status)
            ->get();
    }
    public function updateStatus(int $id, string $newStatus): ?Opportunity
    {
        $opportunity = Opportunity::find($id);

        if ($opportunity) {
            $opportunity->status = $newStatus;
            $opportunity->save();
        }

        return $opportunity;
    }
}
