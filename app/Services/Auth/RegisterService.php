<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Repositories\UserRepository;
use App\Repositories\VolunteerApplicationRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterService
{
    protected $volunteerApplicationRepository;
    protected $userRepository;

    /**
     * Create a new service instance.
     *
     * @param VolunteerApplicationRepository $volunteerApplicationRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        VolunteerApplicationRepository $volunteerApplicationRepository,
        UserRepository $userRepository
    ) {
        $this->volunteerApplicationRepository = $volunteerApplicationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new volunteer user.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        $application = $this->volunteerApplicationRepository->findAcceptedByEmail($data['email']);

        if (!$application) {
            throw new \Exception('البريد الإلكتروني غير موجود في طلبات التطوع المقبولة.');
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \Exception('البريد الإلكتروني مسجل بالفعل. يرجى تسجيل الدخول.');
        }

        $volunteerRoleId = Role::where('name', 'Volunteer')->first()->id;

        try {
            DB::beginTransaction();
            $request = request(); // helper من Laravel
            $request->file('profile_picture');

            $user = $this->userRepository->createVolunteerUser(
                array_merge($data, ['fcm_token' => $data['fcm_token'] ?? null]),
                $application,
                $volunteerRoleId,
                $request->file('profile_picture') // ✅ هذا هو الملف فعليًا
            );

            event(new Registered($user));

            DB::commit();

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('فشل تسجيل المتطوع: ' . $e->getMessage());
            throw $e;
        }
    }

}
