<?php

namespace App\Repositories;

use App\Models\OpportunityApplication;
use Illuminate\Database\Eloquent\Collection;

class EloquentOpportunityApplicationRepository implements OpportunityApplicationRepositoryInterface
{
    public function create(array $data): OpportunityApplication
    {
        return OpportunityApplication::create($data);
    }

    public function findById(int $id): ?OpportunityApplication
    {
        return OpportunityApplication::find($id);
    }

    public function update(int $id, array $data): ?OpportunityApplication
    {
        $application = $this->findById($id);
        if ($application) {
            $application->update($data);
        }
        return $application;
    }

    public function getApplicationsByStatus(string $status): Collection
    {
        return OpportunityApplication::where('status', $status)->get();
    }

    public function getUserApplications(int $userId): Collection
    {
        // eager load opportunity and applicant details
        return OpportunityApplication::where('applicant_user_id', $userId)
            ->with(['opportunity', 'applicant'])
            ->get();
    }
    public function existsForUserAndOpportunity(int $userId, int $opportunityId): bool
    {
        return OpportunityApplication::where('applicant_user_id', $userId)
            ->where('opportunity_id', $opportunityId)
            ->exists();
    }

}
