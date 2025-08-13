<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BeneficiaryService
{
    public function submitRequest(array $data)
    {
        DB::beginTransaction();
        try {
            // صورة الملف الشخصي
            $profilePath = isset($data['profile_picture'])
                ? $data['profile_picture']->store('profiles', 'public')
                : null;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'password' => Hash::make($data['password']),
                'profile_picture_url' => $profilePath,
                'status' => 'pending_verification',
                'role_id' => 6, // نفترض أن 3 تعني "مستفيد"
            ]);

            $user->beneficiaryDetail()->create([
                'beneficiary_type_id' => $data['beneficiary_type_id'],
                'civil_status' => $data['civil_status'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'address' => $data['address'] ?? null,
                'family_members_count' => $data['family_members_count'] ?? null,
                'case_details' => $data['case_details'] ?? null,
                'registration_date' => $data['registration_date'],
            ]);

            // الوثائق
            if (!empty($data['documents'])) {
                foreach ($data['documents'] as $doc) {
                    $filePath = $doc['file']->store('documents', 'public');
                    $user->documents()->create([
                        'document_type' => $doc['document_type'],
                        'file_path' => $filePath,
                        'file_name' => $doc['file']->getClientOriginalName(),
                        'mime_type' => $doc['file']->getClientMimeType(),
                        'notes' => $doc['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
