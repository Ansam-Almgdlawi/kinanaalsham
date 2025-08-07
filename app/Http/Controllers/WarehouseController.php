<?php

namespace App\Http\Controllers;

use App\Models\InKindDonation;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'category' => 'required|in:food,clothes,heating',
        ]);

        Warehouse::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المستودع بنجاح.',
        ]);
    }

    public function addDonation($warehouseId, $donationId)
    {
        $donation = InKindDonation::findOrFail($donationId);
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->category !== $donation->category) {
            return response()->json([
                'success' => false,
                'message' => 'فئة التبرع لا تتطابق مع فئة المستودع.',
            ], 400);
        }

        $item = InventoryItem::firstOrCreate(
            [
                'warehouse_id' => $warehouseId,
                'name' => $donation->item_name,
                'category' => $donation->category,
                'unit' => $donation->unit,
            ],
            [
                'quantity_on_hand' => 0,
                'entry_date' => now(),
            ]
        );

        $item->increment('quantity_on_hand', $donation->quantity);

        $donation->update(['status' => 'added_to_inventory']);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة التبرع إلى المستودع.',
        ]);
    }

    public function inventory($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        return response()->json([
            'warehouse' => $warehouse->name,
            'items' => $warehouse->inventoryItems()->get(['name', 'quantity_on_hand', 'unit', 'entry_date']),
        ]);
    }
}
