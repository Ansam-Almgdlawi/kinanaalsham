<?php

namespace App\Console\Commands;

use App\Models\BeneficiaryDetail;
use App\Models\Fund;
use App\Models\MonthlyDistribution;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DistributeMonthlyFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'funds:distribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distributes monthly funds and updates beneficiary balances.';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $this->info('Starting monthly fund distribution...');

        $totalDistributionAmount = 100000;
        $mainFund = Fund::find(1);

        if (!$mainFund || $mainFund->balance < $totalDistributionAmount) {
            $this->error('Distribution failed. Insufficient funds.');
            return 1;
        }

        $beneficiaries = BeneficiaryDetail::with('type')->whereHas('user', function ($q) {
            $q->where('status', 'active');
        })->get();

        if ($beneficiaries->isEmpty()) {
            $this->info('No active beneficiaries found.');
            return 0;
        }

        $totalPoints = 0;
        $beneficiaryData = [];

        foreach ($beneficiaries as $beneficiary) {
            $points = 0;
            $points += match ($beneficiary->type->name) {
                'يتيم' => 10, 'مسن' => 8, 'أسرة مكفولة' => 5, default => 0,
            };
            $points += match ($beneficiary->civil_status) {
                'widowed' => 5, 'divorced' => 3, default => 0,
            };
            $points += ($beneficiary->family_members_count ?? 1) * 2;

            if ($points > 0) {
                $beneficiaryData[$beneficiary->user_id] = ['points' => $points, 'model' => $beneficiary];
                $totalPoints += $points;
            }
        }

        if ($totalPoints === 0) {
            $this->error('Total points are zero. Cannot distribute.');
            return 1;
        }

        // استخدام Transaction لضمان تنفيذ كل العمليات معاً أو لا شيء
        DB::transaction(function () use ($mainFund, $totalDistributionAmount, $beneficiaryData, $totalPoints) {
            // خصم المبلغ الإجمالي من حساب الجمعية
            $mainFund->decrement('balance', $totalDistributionAmount);

            $now = Carbon::now();
            // حذف سجلات التوزيع للشهر الحالي (لمنع التكرار عند إعادة التشغيل)
            MonthlyDistribution::where('month', $now->month)->where('year', $now->year)->delete();

            // *** التعديل الجوهري هنا ***
            // استخدام حلقة و create() بدلاً من insert()
            foreach ($beneficiaryData as $userId => $data) {
                $share = round(($data['points'] / $totalPoints) * $totalDistributionAmount, 2);

                // 1. إنشاء سجل التوزيع (سيقوم بملء created_at تلقائياً)
                MonthlyDistribution::create([
                    'user_id' => $userId,
                    'amount' => $share,
                    'month' => $now->month,
                    'year' => $now->year,
                ]);

                // 2. تحديث رصيد المستفيد
                $data['model']->increment('balance', $share);
            }
        });

        $this->info('Successfully distributed funds to ' . count($beneficiaryData) . ' beneficiaries.');
        return 0;
    }
}
