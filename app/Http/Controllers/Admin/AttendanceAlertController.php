<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceAlert;
use App\Models\Schedule;
use Carbon\Carbon;

class AttendanceAlertController extends Controller
{
    public function index()
    {
        $this->runCheck();

        $alerts = AttendanceAlert::with(['schedule.assignment.classRoom', 'schedule.assignment.subject', 'schedule.assignment.teacher'])
            ->orderBy('date', 'desc')
            ->get();

        AttendanceAlert::where('is_read', false)->update(['is_read' => true]);

        return view('admin.attendance-alerts.index', compact('alerts'));
    }

    private function runCheck(): void
    {
        $schedules = Schedule::with(['assignment.classRoom', 'assignment.subject', 'assignment.teacher'])->get();

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $startCheck = $today->copy()->subDays(13); // آخر 14 يوم بما فيها اليوم

        foreach ($schedules as $schedule) {
            $cursor = $startCheck->copy();

            while ($cursor->lte($today)) {
                if ($cursor->dayOfWeekIso === (int) $schedule->day_of_week) {

                    // إذا كان اليوم الحالي، نتأكد إن وقت انتهاء الحصة فعلاً مضى قبل ما نعتبرها "فائتة"
                    if ($cursor->isSameDay($today)) {
                        $endTime = Carbon::parse($cursor->format('Y-m-d').' '.$schedule->end_time);
                        if ($now->lt($endTime)) {
                            $cursor->addDay();
                            continue;
                        }
                    }

                    $hasAttendance = Attendance::where('schedule_id', $schedule->id)
                        ->where('date', $cursor->format('Y-m-d'))
                        ->exists();

                    $alreadyAlerted = AttendanceAlert::where('schedule_id', $schedule->id)
                        ->where('date', $cursor->format('Y-m-d'))
                        ->exists();

                    if (! $hasAttendance && ! $alreadyAlerted) {
                        AttendanceAlert::create([
                            'schedule_id' => $schedule->id,
                            'date' => $cursor->format('Y-m-d'),
                            'message' => 'الأستاذ '.$schedule->assignment->teacher->first_name.' '.$schedule->assignment->teacher->last_name
                                .' لم يسجل حضور حصة '.$schedule->assignment->subject->name
                                .' لقسم '.$schedule->assignment->classRoom->name
                                .' بتاريخ '.$cursor->format('Y-m-d'),
                        ]);
                    }
                }
                $cursor->addDay();
            }
        }
    }
}