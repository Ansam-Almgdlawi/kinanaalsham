<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use Exception;

class ProjectVolunteerRepository
{
    public function register(array $data)
    {
        // 1. التحقق من عدم التسجيل المسبق
        $existingRegistration = DB::table('project_volunteer')
            ->where('project_id', $data['project_id'])
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($existingRegistration) {
            throw new Exception('أنت مسجل مسبقًا في هذا المشروع.');
        }

        // 2. جلب بيانات المشروع للتحقق من العدد المسموح
        $project = Project::find($data['project_id']);

        // 3. التحقق من اكتمال العدد (إذا كان `max_volunteers` محددًا)
        if ($project->max_volunteers !== null) {
            $currentVolunteers = DB::table('project_volunteer')
                ->where('project_id', $data['project_id'])
                ->where('status', 'approved')
                ->count();

            if ($currentVolunteers >= $project->max_volunteers) {
                throw new Exception('اكتمل العدد المسموح للمتطوعين في هذا المشروع.');
            }
        }

        // 4. إنشاء التسجيل إذا كل الشروط مطابقة
        return DB::table('project_volunteer')->insertGetId([
            'project_id' => $data['project_id'],
            'user_id' => $data['user_id'],
            'user_type' => $data['user_type'] ?? 'volunteer',
            'status' => $data['status'] ?? 'pending',
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getVolunteerProjects($userId)
    {
        return DB::table('project_volunteer')
            ->join('projects', 'project_volunteer.project_id', '=', 'projects.id')
            ->where('project_volunteer.user_id', $userId)
            ->select(
                'projects.id',
                'projects.name',
                'projects.description',
                'projects.start_date',
                'projects.end_date',
                'project_volunteer.status',
                'project_volunteer.registered_at'
            )
            ->get();
    }
}
