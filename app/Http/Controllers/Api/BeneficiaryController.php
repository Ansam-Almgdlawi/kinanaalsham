<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Resources\BeneficiaryResource;
use App\Models\User;
use App\Services\BeneficiaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        if ($user->role_id !== 3) {
            return response()->json(['message' => 'هذا المستخدم ليس مستفيداً.'], 400);
        }

        $user->update(['status' => $request->status]);

        return response()->json(['message' => 'تم تحديث حالة المستفيد بنجاح.']);
    }

    public function pending()
    {
        $pendingUsers = User::where('role_id', 3)
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

}
