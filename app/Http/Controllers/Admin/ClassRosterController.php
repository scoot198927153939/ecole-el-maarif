<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Enrollment;

class ClassRosterController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with('academicYear')
            ->withCount('enrollments')
            ->orderBy('name')
            ->get();

        return view('admin.students.rosters.index', compact('classes'));
    }

    public function show(ClassRoom $class)
    {
        $enrollments = $this->rosterEnrollments($class);

        return view('admin.students.rosters.show', compact('class', 'enrollments'));
    }

    public function exportCsv(ClassRoom $class)
    {
        $enrollments = $this->rosterEnrollments($class);

        $filename = 'roster-' . str_replace(' ', '-', $class->name) . '.csv';

        $columns = [
            __('messages.students_col_number'),
            __('messages.students_col_full_name'),
            __('messages.students_birth_date_label'),
            __('messages.pdf_birth_place_label'),
            __('messages.pdf_national_id_label'),
            __('messages.pdf_school_number_label'),
            __('messages.status'),
            __('messages.class_rosters_col_guardian_name'),
            __('messages.profession'),
            __('messages.students_col_phone1'),
            __('messages.students_col_whatsapp'),
            __('messages.pdf_phone2_label'),
            __('messages.address'),
        ];

        $statusLabels = [
            'active' => __('messages.students_status_active'),
            'graduated' => __('messages.students_status_graduated'),
            'withdrawn' => __('messages.students_status_withdrawn'),
        ];

        $callback = function () use ($enrollments, $columns, $statusLabels) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns);

            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                $guardian = $student->guardian;

                fputcsv($handle, [
                    $student->student_number,
                    $student->first_name . ' ' . $student->last_name,
                    optional($student->birth_date)->format('Y-m-d'),
                    $student->birth_place,
                    $student->national_id,
                    $student->school_number,
                    $statusLabels[$student->status] ?? $student->status,
                    $guardian->name ?? '',
                    $guardian->profession ?? '',
                    $guardian->phone1 ?? '',
                    $guardian->whatsapp ?? '',
                    $guardian->phone2 ?? '',
                    $guardian->address ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function rosterEnrollments(ClassRoom $class)
    {
        return Enrollment::with(['student.guardian'])
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->get()
            ->sortBy(fn ($e) => $e->student->first_name . ' ' . $e->student->last_name)
            ->values();
    }
}
