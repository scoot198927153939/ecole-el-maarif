<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\StaffAttendance;
use App\Models\StaffMember;
use App\Models\Teacher;
use App\Models\TeacherAttendanceSession;
use Illuminate\Http\Request;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();
        $staffMembers = StaffMember::where('status', 'active')->orderBy('first_name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        $existingSessions = TeacherAttendanceSession::where('date', $date)
            ->get()
            ->groupBy('teacher_id')
            ->map(fn ($sessions) => $sessions->keyBy('session_number'));

        $existingStaffAttendance = StaffAttendance::where('staff_type', 'staff_member')
            ->where('date', $date)
            ->get()
            ->keyBy('staff_id');

        return view('admin.staff-attendance.index', compact(
            'date', 'teachers', 'staffMembers', 'classes', 'existingSessions', 'existingStaffAttendance'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'sessions' => 'nullable|array',
            'staff_status' => 'nullable|array',
            'staff_hours' => 'nullable|array',
        ]);

        foreach ($validated['sessions'] ?? [] as $teacherId => $teacherSessions) {
            foreach ($teacherSessions as $sessionNumber => $data) {
                $status = $data['status'] ?? null;

                if (! $status) {
                    TeacherAttendanceSession::where('teacher_id', $teacherId)
                        ->where('date', $validated['date'])
                        ->where('session_number', $sessionNumber)
                        ->delete();
                    continue;
                }

                TeacherAttendanceSession::updateOrCreate(
                    ['teacher_id' => $teacherId, 'date' => $validated['date'], 'session_number' => $sessionNumber],
                    [
                        'status' => $status,
                        'actual_start' => $status === 'present' ? ($data['actual_start'] ?? null) : null,
                        'actual_end' => $status === 'present' ? ($data['actual_end'] ?? null) : null,
                        'class_id' => $data['class_id'] ?: null,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }
        }

        foreach ($validated['staff_status'] ?? [] as $staffId => $status) {
            StaffAttendance::updateOrCreate(
                ['staff_type' => 'staff_member', 'staff_id' => $staffId, 'date' => $validated['date']],
                [
                    'status' => $status,
                    'hours' => $status === 'present' ? ($validated['staff_hours'][$staffId] ?? null) : null,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('admin.staff-attendance.index', ['date' => $validated['date']])
            ->with('success', __('messages.flash_staff_attendance_saved', ['date' => $validated['date']]));
    }
}