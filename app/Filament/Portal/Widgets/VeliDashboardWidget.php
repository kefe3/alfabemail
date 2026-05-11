<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Veli;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use App\Models\ActivityLog;
use App\Services\MailcowService;
use App\Services\VeliAnalizService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VeliDashboardWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.veli-dashboard';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $user = Auth::user();
        $veli = $user->veli;

        if (!$veli) {
            return $this->emptyData();
        }

        $students = $veli->ogrenciler()->with('user', 'sinif')->get();
        $analizService = app(VeliAnalizService::class);
        $mailcow = app(MailcowService::class);

        $studentsData = [];
        $allWeeklyTraffic = [];
        $allMailSummary = [];
        $allInsights = [];
        $totalMails = 0;
        $topContacts = collect();

        foreach ($students as $student) {
            $analiz = $analizService->analyze($student);
            $quotaInfo = $this->getQuotaInfo($student, $mailcow);
            $aktiviteLoglari = $this->getAktiviteLoglari($student);
            $mailSummary = $this->getMailSummary($student);

            $studentsData[] = [
                'student' => $student,
                'analiz' => $analiz,
                'quota' => $quotaInfo,
                'aktivite_loglari' => $aktiviteLoglari,
                'mail_summary' => $mailSummary,
            ];

            $totalMails += $analiz['insights']['total_emails'];
            $allInsights[] = $analiz['insights'];

            foreach ($analiz['insights']['top_contacts'] as $contact) {
                $topContacts->push($contact);
            }

            foreach ($analiz['weekly_traffic'] as $dayData) {
                $day = $dayData['day'];
                if (!isset($allWeeklyTraffic[$day])) {
                    $allWeeklyTraffic[$day] = 0;
                }
                $allWeeklyTraffic[$day] += $dayData['count'];
            }

            foreach ($mailSummary as $m) {
                $allMailSummary[] = $m;
            }
        }

        $days = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
        $weeklyTraffic = [];
        foreach ($days as $day) {
            $weeklyTraffic[] = [
                'day' => $day,
                'count' => $allWeeklyTraffic[$day] ?? 0,
            ];
        }

        usort($allMailSummary, fn($a, $b) => strcmp($b['time'] ?? '', $a['time'] ?? ''));
        $allMailSummary = array_slice($allMailSummary, 0, 10);

        $topContact = $topContacts->count() > 0
            ? $topContacts->groupBy(fn($i) => $i)->sortDesc()->keys()->first()
            : '-';

        $combinedAnaliz = $this->combineInsights($allInsights);

        $weeklySummary = $this->generateWeeklySummary($students, $totalMails, $topContact);

        return [
            'students' => $students,
            'students_data' => $studentsData,
            'stats' => [
                'weekly_mail_count' => $totalMails,
                'top_contact' => $topContact,
                'student_count' => $students->count(),
                'total_weekly' => collect($weeklyTraffic)->sum('count'),
            ],
            'chartData' => [
                'weekly_traffic' => $weeklyTraffic,
                'mail_summary' => $allMailSummary,
            ],
            'combined_analiz' => $combinedAnaliz,
            'weekly_summary' => $weeklySummary,
            'single_student' => $students->count() === 1 ? $students->first() : null,
        ];
    }

    private function getQuotaInfo(Ogrenci $student, MailcowService $mailcow): array
    {
        if (!$student->mailbox_local_part) {
            return ['percent' => 0, 'used' => 0, 'total' => 0, 'ok' => true];
        }

        try {
            $email = $student->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
            $quota = $mailcow->getMailboxQuota($email);
            $percent = $quota['percent_used'] ?? 0;
            return [
                'percent' => $percent,
                'used' => $quota['quota_used'] ?? 0,
                'total' => $quota['quota'] ?? 0,
                'ok' => $percent < 80,
            ];
        } catch (\Exception $e) {
            return ['percent' => 0, 'used' => 0, 'total' => 0, 'ok' => true];
        }
    }

    private function getAktiviteLoglari(Ogrenci $student): array
    {
        return ActivityLog::where('user_id', $student->user_id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getMailSummary(Ogrenci $student): array
    {
        $logs = MailAktiviteLog::where('ogrenci_id', $student->id)
            ->where('tarih', '>=', Carbon::now()->subDays(7))
            ->orderBy('tarih', 'desc')
            ->take(10)
            ->get();

        $summary = [];
        foreach ($logs as $log) {
            $summary[] = [
                'type' => $log->tip === 'gonderilen' ? '📤 Giden' : '📥 Gelen',
                'from' => $log->kimden ?? '-',
                'subject' => $log->konu ?? '-',
                'time' => $log->tarih ? $log->tarih->format('d H:i') : '-',
                'student' => $student->user->name,
            ];
        }

        return $summary;
    }

    private function combineInsights(array $allInsights): array
    {
        $total = 0;
        $incoming = 0;
        $outgoing = 0;
        $uniqueContacts = 0;
        $allContacts = collect();
        $hourlyAll = [];

        foreach ($allInsights as $ins) {
            $total += $ins['total_emails'];
            $incoming += $ins['incoming'];
            $outgoing += $ins['outgoing'];
            $uniqueContacts += $ins['unique_contacts'];
            foreach ($ins['top_contacts'] as $c) {
                $allContacts->push($c);
            }
            foreach (($ins['hourly_distribution'] ?? []) as $hour => $count) {
                $hourlyAll[$hour] = ($hourlyAll[$hour] ?? 0) + $count;
            }
        }

        $busiestHour = $hourlyAll ? array_search(max($hourlyAll), $hourlyAll) : null;

        return [
            'total_emails' => $total,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'unique_contacts' => $uniqueContacts,
            'top_contacts' => $allContacts->unique()->take(5)->values()->toArray(),
            'busiest_hour' => $busiestHour ? $busiestHour . ':00' : null,
        ];
    }

    private function generateWeeklySummary($students, int $totalMails, string $topContact): array
    {
        $studentNames = $students->map(fn($s) => $s->user->name)->implode(', ');
        $weekNumber = Carbon::now()->weekOfYear;

        return [
            'week' => $weekNumber,
            'period' => Carbon::now()->startOfWeek()->format('d M') . ' - ' . Carbon::now()->endOfWeek()->format('d M Y'),
            'students' => $studentNames,
            'total_mails' => $totalMails,
            'top_contact' => $topContact,
            'generated_at' => Carbon::now()->format('d.m.Y H:i'),
        ];
    }

    private function emptyData(): array
    {
        $days = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
        $empty = [];
        foreach ($days as $day) {
            $empty[] = ['day' => $day, 'count' => 0];
        }

        return [
            'students' => collect(),
            'students_data' => [],
            'stats' => [
                'weekly_mail_count' => 0,
                'top_contact' => '-',
                'student_count' => 0,
                'total_weekly' => 0,
            ],
            'chartData' => [
                'weekly_traffic' => $empty,
                'mail_summary' => [],
            ],
            'combined_analiz' => [
                'total_emails' => 0,
                'incoming' => 0,
                'outgoing' => 0,
                'unique_contacts' => 0,
                'top_contacts' => [],
                'busiest_hour' => null,
            ],
            'weekly_summary' => null,
            'single_student' => null,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('veli') ?? false;
    }
}
