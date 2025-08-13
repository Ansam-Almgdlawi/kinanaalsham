<?php

namespace App\Http\Controllers;

use App\Models\InKindDonation;
use Illuminate\Http\Request;

class DonationController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'required|string',
            'category' => 'required|in:food,clothes,heating',
            'item_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
        ]);

        $data['donated_at'] = now();

        InKindDonation::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل التبرع بنجاح.',
        ]);
    }
    public function pendingDonations()
    {
        $donations = InKindDonation::where('status', 'pending')
            ->orderByDesc('donated_at')
            ->get([
                'id',
                'phone_number',
                'category',
                'item_name',
                'quantity',
                'unit',
                'donated_at'
            ]);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

}
