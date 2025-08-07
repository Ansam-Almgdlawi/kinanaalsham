<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryDetail;
use App\Models\Distribution;
use App\Models\Event;
use App\Models\InKindDonation;
use App\Models\User;
use App\Models\VolunteerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function monthlyReport($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();

        $events = Event::whereBetween('start_datetime', [$startDate, $endDate])->get();
        $eventCount = $events->count();
        $eventDetails = $events->map(fn($e) => [
            'الاسم' => $e->name,
            'التاريخ' => $e->event_date,
            'الوصف' => $e->description,
            'المكان' => $e->location,
        ]);

        $inKindDonations = InKindDonation::whereBetween('created_at', [$startDate, $endDate])->get();
        $donationCount = $inKindDonations->count();
        $donationsByItem = $inKindDonations->groupBy('item_name')->map(fn($group) => $group->sum('quantity'));

        $distributions = Distribution::whereBetween('created_at', [$startDate, $endDate])->with('records')->get();
        $distributionCount = $distributions->count();
        $distributionDetails = $distributions->map(function ($distribution) {
            $total = $distribution->records->count();
            $received = $distribution->records->where('has_received', true)->count();
            return [
                'العنوان' => $distribution->title,
                'نوع المستفيد' => $distribution->beneficiaryType->name ?? 'غير محدد',
                'عدد المستفيدين' => $total,
                'عدد المستلمين فعليًا' => $received,
                'نسبة الإنجاز' => $total > 0 ? round(($received / $total) * 100, 2) . '%' : '0%',
            ];
        });

        $newBeneficiaries = User::whereHas('beneficiaryDetail')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return response()->json([
            'الشهر' => $startDate->translatedFormat('F Y'),
            'عدد الفعاليات' => $eventCount,
            'تفاصيل الفعاليات' => $eventDetails,
            'عدد التبرعات العينية' => $donationCount,
            'تفصيل التبرعات العينية حسب الصنف' => $donationsByItem,
            'عدد التوزيعات' => $distributionCount,
            'تفاصيل التوزيعات' => $distributionDetails,
            'عدد المستفيدين الجدد هذا الشهر' => $newBeneficiaries,
        ]);
    }
    public function getDashboardStats()
    {
        // عدد الفعاليات المنظمة
        $eventsCount = Event::count();

        // إجمالي الساعات التطوعية
        $totalVolunteerHours = VolunteerDetail::sum('total_hours_volunteered');

        // عدد التوزيعات العينية
        $distributionsCount = Distribution::count();

        // عدد المتطوعين الحاليين = عدد السجلات في volunteer_details
        $currentVolunteersCount = VolunteerDetail::count();

        // عدد المستفيدين من كل صنف
        $orphansCount = BeneficiaryDetail::whereHas('type', function ($query) {
            $query->where('name', 'يتيم');
        })->count();

        $elderlyCount = BeneficiaryDetail::whereHas('type', function ($query) {
            $query->where('name', 'مسن');
        })->count();

        $familiesCount = BeneficiaryDetail::whereHas('type', function ($query) {
            $query->where('name', 'أسرة مكفولة');
        })->count();

        $data = [
            [
                'العنوان' => 'عدد الفعاليات المنظمة',
                'القيمة' => $eventsCount,
                'الوحدة' => 'فعالية'
            ],
            [
                'العنوان' => 'إجمالي الساعات التطوعية',
                'القيمة' => $totalVolunteerHours,
                'الوحدة' => 'ساعة'
            ],
            [
                'العنوان' => 'عدد التوزيعات العينية',
                'القيمة' => $distributionsCount,
                'الوحدة' => 'توزيع'
            ],
            [
                'العنوان' => 'عدد المتطوعين الحاليين',
                'القيمة' => $currentVolunteersCount,
                'الوحدة' => 'متطوع'
            ],
            [
                'العنوان' => 'عدد الأيتام المستفيدين',
                'القيمة' => $orphansCount,
                'الوحدة' => 'مستفيد'
            ],
            [
                'العنوان' => 'عدد المسنين المستفيدين',
                'القيمة' => $elderlyCount,
                'الوحدة' => 'مستفيد'
            ],
            [
                'العنوان' => 'عدد الأسر المكفولة',
                'القيمة' => $familiesCount,
                'الوحدة' => 'أسرة'
            ],
        ];

        return response()->json([
            'success' => true,
            'detail'=> $data]);
    }

}
