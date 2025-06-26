<?php

namespace App\Repositories;

use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Collection;

interface OpportunityRepositoryInterface
{
    /**
     * إنشاء فرصة جديدة في قاعدة البيانات.
     *
     * @param array $data
     * @return Opportunity
     */
    public function create(array $data): Opportunity;
    public function getOpportunitiesByTypeAndStatus(string $type, string $status): Collection;
    public function updateStatus(int $id, string $newStatus): ?Opportunity;
}
