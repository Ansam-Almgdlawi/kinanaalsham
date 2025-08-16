<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectPostRequest;
use App\Models\Project;
use App\Models\ProjectPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectPostController extends Controller
{
    public function store(CreateProjectPostRequest $request): JsonResponse
    {
        try {
            // التحقق من صلاحية المستخدم (أدمن أو مدير مشروع)
            $user = auth()->user();
            if (!in_array($user->role_id, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'عفواً، هذا الإجراء مسموح فقط للأدمن ومدير المشروع'
                ], 403);
            }

            // البحث عن المشروع الربحي
            $project = Project::findOrFail($request->project_id);

            // رفع الملفات إذا وجدت
            $mediaPaths = [];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $mediaPaths[] = $file->store('project_posts', 'public');
                }
            }

            $validatedData = $request->validate([
                'project_id' => 'required|exists:events,id',
                'content' => 'required|string|min:3', // تأكد من وجود محتوى
            ]);

            $post = ProjectPost::create([
                'project_id' => $project->id,
                'admin_id' => auth()->id(),
                'content' => $validatedData['content'],
                'media' => $mediaPaths ?: null
            ]);
            return response()->json([
                'success' => true,
                'message' => 'تم نشر المنشور بنجاح',
                'data' => $post
            ], 201);

        } catch (\Exception $e) {
            // حذف الملفات المرفوعة في حالة حدوث خطأ
            if (!empty($mediaPaths)) {
                foreach ($mediaPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء نشر المنشور: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض المنشورات الخاصة بالفعاليات الربحية
     */
    public function getPublishedProjects(): JsonResponse
    {
        try {
            $posts = ProjectPost::with(['project', 'admin']) // تحميل العلاقات
            ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts->map(function ($post) {
                    // معالجة الميديا (يمكن أن تكون متعددة)
                    $mediaUrls = [];
                    if ($post->media && is_array($post->media)) {
                        foreach ($post->media as $media) {
                            $mediaUrls[] = asset('storage/' . $media);
                        }
                    }

                    return [
                        'id' => $post->id,
                        'content' => $post->content,
                        'media' => $mediaUrls,
                        'project_name' => $post->project->name ?? null,
                        'project_number' => $post->project->project_number ?? null,
                        'admin_name' => $post->admin->name ?? null,
                        'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                        'project_details' => [
                            'budget' => $post->project->budget ?? null,
                            'objective' => $post->project->objective ?? null,
                            'status' => $post->project->status ?? null
                        ]
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنشورات: ' . $e->getMessage()
            ], 500);
        }
    }

}
