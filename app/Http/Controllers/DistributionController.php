<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryDetail;
use App\Models\Distribution;
use App\Models\DistributionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'beneficiary_type_id' => 'required|exists:beneficiary_types,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity_per_user' => 'required|numeric|min:0.01'
        ]);

        $distribution = Distribution::create($data);

        $beneficiaries = BeneficiaryDetail::where('beneficiary_type_id', $data['beneficiary_type_id'])->pluck('user_id');

        foreach ($beneficiaries as $userId) {
            DistributionRecord::create([
                'distribution_id' => $distribution->id,
                'user_id' => $userId,
                'has_received' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التوزيع وربط المستفيدين.',
        ]);
    }


    // تحديث حالة مستلم إلى 1
    public function markReceived(Request $request, $distributionId)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $distribution = Distribution::with('item')->findOrFail($distributionId);
        $record = DistributionRecord::where('distribution_id', $distributionId)
            ->where('user_id', $data['user_id'])
            ->firstOrFail();

        if ($record->has_received) {
            return response()->json([
                'success' => false,
                'message' => 'تم الاستلام مسبقًا.',
            ], 400);
        }

        $item = $distribution->item;
        $requiredQty = $distribution->quantity_per_user;

        if ($item->quantity_on_hand < $requiredQty) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد كمية كافية في المستودع.',
            ], 400);
        }

        DB::transaction(function () use ($record, $item, $requiredQty) {
            $record->update(['has_received' => true]);
            $item->decrement('quantity_on_hand', $requiredQty);
        });

        return response()->json([
            'success' => true,
            'message' => 'تم الاستلام وتحديث المخزون.',
        ]);
    }


    // عرض حالة توزيع معينة
    public function show($distributionId)
    {
        $distribution = Distribution::with(['records.user'])->findOrFail($distributionId);

        $records = $distribution->records->map(function ($record) {
            return [
                'user_id' => $record->user->id,
                'name' => $record->user->name,
                'phone' => $record->user->phone_number,
                'has_received' => $record->has_received,
            ];
        });

        return response()->json([
            'title' => $distribution->title,
            'beneficiary_type' => $distribution->beneficiaryType->name,
            'records' => $records,
        ]);
    }
}
