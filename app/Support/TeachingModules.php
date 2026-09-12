<?php

namespace App\Support;

class TeachingModules
{
    /**
     * وحدات التدريس اليومي. الأستاذ يملكها كلها تلقائياً دائماً؛
     * القائمة هنا تُستخدم فقط لمنح مستخدم آخر (كالمشرف) وصولاً اختيارياً لبعضها.
     */
    public static function all(): array
    {
        return [
            ['key' => 'assessments', 'route' => 'assessments.index', 'label' => 'card_assessments_title', 'icon' => '📋'],
            ['key' => 'gradebook', 'route' => 'gradebook.classes', 'label' => 'card_gradebook_title', 'icon' => '📖'],
            ['key' => 'grades', 'route' => 'assessments.index', 'label' => 'grades_module_title', 'icon' => '✏️'],
            ['key' => 'attendance_take', 'route' => 'attendance.select', 'label' => 'card_attendance_take_title', 'icon' => '✅'],
            ['key' => 'attendance_reports', 'route' => 'attendance.report.select', 'label' => 'card_attendance_report_title', 'icon' => '📊'],
            ['key' => 'lesson_logs', 'route' => 'lesson-logs.index', 'label' => 'card_lesson_logs_title', 'icon' => '📷'],
            ['key' => 'student_profile', 'route' => 'gradebook.classes', 'label' => 'student_profile_module_title', 'icon' => '🗂️'],
            ['key' => 'reports', 'route' => 'assessments.index', 'label' => 'reports_module_title', 'icon' => '🧾'],
        ];
    }

    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }

    /**
     * خريطة كل أسماء الروابط المحمية بميدلوير admin_or_teacher إلى مفتاح صلاحية التدريس المطابق لها.
     */
    private static function routeMap(): array
    {
        return [
            'gradebook.classes' => 'gradebook',
            'gradebook.subjects' => 'gradebook',
            'gradebook.grid' => 'gradebook',
            'gradebook.store' => 'gradebook',

            'assessments.bulk-create' => 'assessments',
            'assessments.bulk-store' => 'assessments',
            'assessments.index' => 'assessments',
            'assessments.create' => 'assessments',
            'assessments.store' => 'assessments',
            'assessments.edit' => 'assessments',
            'assessments.update' => 'assessments',
            'assessments.destroy' => 'assessments',

            'grades.index' => 'grades',
            'grades.store' => 'grades',

            'lesson-logs.index' => 'lesson_logs',
            'lesson-logs.create' => 'lesson_logs',
            'lesson-logs.store' => 'lesson_logs',
            'lesson-logs.edit' => 'lesson_logs',
            'lesson-logs.update' => 'lesson_logs',
            'lesson-logs.destroy' => 'lesson_logs',
            'lesson-logs.photos.destroy' => 'lesson_logs',

            'attendance.select' => 'attendance_take',
            'attendance.schedules' => 'attendance_take',
            'attendance.index' => 'attendance_take',
            'attendance.store' => 'attendance_take',

            'attendance.report.select' => 'attendance_reports',
            'attendance.report.daily' => 'attendance_reports',
            'attendance.report.monthly' => 'attendance_reports',

            'students.profile' => 'student_profile',

            'reports.show' => 'reports',
            'reports.term.pdf' => 'reports',
            'reports.term1.pdf' => 'reports',
            'reports.term2.pdf' => 'reports',
            'reports.term3.pdf' => 'reports',
            'reports.class.term1.pdf' => 'reports',
            'reports.class.term2.pdf' => 'reports',
            'reports.class.term3.pdf' => 'reports',
            'grades.pdf' => 'reports',
            'assessments.pdf' => 'reports',
        ];
    }

    public static function permissionKeyForRoute(string $routeName): ?string
    {
        return self::routeMap()[$routeName] ?? null;
    }
}
