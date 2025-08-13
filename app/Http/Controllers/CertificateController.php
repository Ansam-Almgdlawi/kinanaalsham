<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CertificateController extends Controller
{

    public function getUserCertificates()
    {
        $user = Auth::user();

        // نقوم بتحميل العلاقات اللازمة (user و event) لتجنب استعلامات N+1
        $certificates = $user->certificates()->with(['user', 'event'])->get();

        return response()->json([
            'success' => true,
            'certificates' => CertificateResource::collection($certificates)
        ]);
    }
}
