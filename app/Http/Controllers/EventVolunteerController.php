<?php



namespace App\Http\Controllers;

use App\Http\Requests\RegisterVolunteerRequest;
use App\Services\EventVolunteerService;
use App\Http\Resources\EventVolunteerResource;

class EventVolunteerController extends Controller
{
    protected $eventVolunteerService;

    public function __construct(EventVolunteerService $eventVolunteerService)
    {
        $this->eventVolunteerService = $eventVolunteerService;
    }

    public function register(RegisterVolunteerRequest $request, $eventId)
    {
        try {
            $user = auth()->user();


            //if ($user->role_id !== 5) {
            // return response()->json([
            //  'success' => false,
            //'message' => 'عفواً، التسجيل في الفعاليات متاح فقط للمتطوعين المسجلين'
            // ], 403);
            // }

            $data = [
                'event_id' => $eventId, // نأخذها من الـ URL
                'user_id' => $user->id  // نأخذها من المستخدم المسجل
            ];

            $result = $this->eventVolunteerService->registerVolunteer($data);

            return $result;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function getMyEvents()
    {
        $user = auth()->user();


        if ($user->role_id !== 5) {
            return response()->json([
                'success' => false,
                'message' => 'عفواً، هذا الاجراء متاح فقط للمتطوعين المسجلين'
            ], 403);
        }


        return $this->eventVolunteerService->getVolunteerEvents($user);
    }

}
