<?php

namespace App\Services;

use App\Repositories\ProjectVolunteerRepository;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectVolunteerService
{
    protected $projectVolunteerRepo;

    public function __construct(ProjectVolunteerRepository $projectVolunteerRepo)
    {
        $this->projectVolunteerRepo = $projectVolunteerRepo;
    }

    public function registerVolunteer(array $data)
    {
        try {
            $registrationId = $this->projectVolunteerRepo->register([
                'project_id' => $data['project_id'],
                'user_id' => $data['user_id'],
                'user_type' => 'volunteer',
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تقديم طلب التطوع بنجاح',
                'registration_id' => $registrationId
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function getRemainingSlots($projectId)
    {
        $project = Project::find($projectId);
        if ($project->max_volunteers === null) return 'غير محدود';

        $currentVolunteers = DB::table('project_volunteer')
            ->where('project_id', $projectId)
            ->where('status', 'approved')
            ->count();

        return $project->max_volunteers - $currentVolunteers;
    }

    public function getVolunteerProjects($userId)
    {
        try {
            $projects = $this->projectVolunteerRepo->getVolunteerProjects($userId);

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
