<?php

namespace App\Repositories;

use App\Models\OpportunityApplication;
use Illuminate\Database\Eloquent\Collection;

interface OpportunityApplicationRepositoryInterface
{
    public function create(array $data): OpportunityApplication;
    public function findById(int $id): ?OpportunityApplication;
    public function update(int $id, array $data): ?OpportunityApplication;
    public function getApplicationsByStatus(string $status): Collection;
    public function getUserApplications(int $userId): Collection;
}
