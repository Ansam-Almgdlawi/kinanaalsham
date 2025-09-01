<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryProfileRequest;
use App\Http\Resources\BeneficiaryProfileResource;
use App\Http\Resources\BeneficiaryResource;
use App\Models\BeneficiaryDocument;
use App\Models\BeneficiaryType;
use App\Models\User;
use App\Services\BeneficiaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BeneficiaryController extends Controller
{
    public function __construct(protected BeneficiaryService $service) {}

    public function store(StoreBeneficiaryRequest $request)
    {
        $user = $this->service->submitRequest($request->validated());
        return response()->json(['message' => 'تم إرسال الطلب بنجاح، بانتظار المراجعة.', 'user_id' => $user->id], 201);
    }


    public function updateStatus(Request $request, $userId)
    {
        if (auth()->user()->role_id !== 1) {
            return response()->json(['message' => 'غير مصرح لك بتنفيذ هذا الإجراء.'], 403);
        }

        $request->validate([
            'status' => 'required|in:active,rejected',
        ]);

        $user = User::findOrFail($userId);

        if ($user->role_id !== 6) {
            return response()->json(['message' => 'هذا المستخدم ليس مستفيداً.'], 400);
        }

        $user->update(['status' => $request->status]);

        return response()->json(['message' => 'تم تحديث حالة المستفيد بنجاح.']);
    }

    public function pending()
    {
        $pendingUsers = User::where('role_id', 6)
        ->where('status', 'pending_verification')
            ->with(['beneficiaryDetail', 'documents'])
            ->get();

        return response()->json([
            'message' => 'قائمة الطلبات غير المقبولة',
            'data' => BeneficiaryResource::collection($pendingUsers)
        ]);
    }

    public function show(User $user)
    {
        if ($user->role_id !== 3) {
            return response()->json(['message' => 'المستخدم ليس مستفيداً'], 403);
        }

        $user->load(['beneficiaryDetail', 'documents']);

        return response()->json([
            'message' => 'تفاصيل المستفيد',
            'data' => new BeneficiaryResource($user),
        ]);
    }
    public function profile()
    {
        $user = Auth::user()->load(['details', 'documents']);

        return new BeneficiaryProfileResource($user);
    }
    public function updateProfile(UpdateBeneficiaryProfileRequest $request)
    {
        $user = auth()->user();
        $details = $user->details;

        if (!$details) {
            return response()->json(['message' => 'لا توجد بيانات تفاصيل للمستفيد.'], 404);
        }

        $details->civil_status = $request->input('civil_status', $details->civil_status);
        $details->gender = $request->input('gender', $details->gender);
        $details->address = $request->input('address', $details->address);
        $details->family_members_count = $request->input('family_members_count', $details->family_members_count);
        $details->case_details = $request->input('case_details', $details->case_details);

        if ($request->has('documents')) { // تحقق من وجود مصفوفة 'documents'
            $documentsData = $request->input('documents');

            foreach ($documentsData as $index => $documentItem) {
                // تحقق من وجود ملف مرفوع فعليًا في هذا العنصر
                if ($request->hasFile("documents.{$index}.file")) {

                    $uploadedFile = $request->file("documents.{$index}.file");
                    $path = $uploadedFile->store('documents', 'public');

                    BeneficiaryDocument::create([
                        'beneficiary_user_id' => $user->id,
                        'file_name' => $documentItem['name'] ?? null,
                        'file_path' => $path,
                        'document_type' => $documentItem['type'] ?? null,
                        'status' => 'pending',
                    ]);
                }
            }

            $details->last_document_update = now();
        }

        $details->save();

     //   dd($request->all());

        return response()->json([
            'message' => 'تم تحديث بياناتك بنجاح',
        ]);
    }
    public function destroy(BeneficiaryDocument $document)
    {

        if (auth()->id() !== $document->beneficiary_user_id) {
            return response()->json(['message' => 'غير مصرح لك بتنفيذ هذا الإجراء.'], 403); // 403 Forbidden
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'تم حذف المستند بنجاح.']);
    }
    public function getByType($typeName)
    {
        $type = BeneficiaryType::where('name', $typeName)->firstOrFail();

        $beneficiaries = $type->beneficiaries()
            ->with('user')
            ->get()
            ->map(function ($beneficiary) {
                return [
                    'id' => $beneficiary->user->id,
                    'name' => $beneficiary->user->name,
                    'civil_status' => $beneficiary->civil_status,
                    'family_members_count' => $beneficiary->family_members_count,
                ];
            });

        return response()->json([
            'type' => $type->name,
            'beneficiaries' => $beneficiaries
        ]);
    }
    ////////////
    public function showBalance(Request $request): JsonResponse
    {
        $user = Auth::user();
        $beneficiaryDetail = $user->beneficiaryDetail;

        if (!$beneficiaryDetail) {
            return response()->json(['message' => 'Beneficiary details not found.'], 404);
        }

        return response()->json([
            'name' => $user->name,
            'current_balance' => $beneficiaryDetail->balance,
        ]);
    }


}
