<?php

namespace App\Support;

class AdminModules
{
    /**
     * كل الوحدات القابلة للمنح كصلاحية لمستخدم غير أدمن (مشرف أو أستاذ).
     * المفتاح (key) يطابق دائماً الجزء الثاني من اسم الراوت بعد "admin." (بالشرطة السفلية بدل الفاصلة).
     * صفحة "المستخدمون" مستثناة عمداً — تبقى حكراً على الأدمن الحقيقي فقط.
     */
    public static function all(): array
    {
        return [
            // الشؤون الأكاديمية
            ['key' => 'academic_years', 'route' => 'admin.academic-years.index', 'label' => 'card_academic_years_title', 'icon' => '📅', 'category' => 'academic'],
            ['key' => 'classes', 'route' => 'admin.classes.index', 'label' => 'card_classes_title', 'icon' => '🏫', 'category' => 'academic'],
            ['key' => 'students', 'route' => 'admin.students.index', 'label' => 'card_students_title', 'icon' => '🎓', 'category' => 'academic'],
            ['key' => 'enrollments', 'route' => 'admin.enrollments.index', 'label' => 'card_enrollments_title', 'icon' => '📝', 'category' => 'academic'],
            ['key' => 'subjects', 'route' => 'admin.subjects.index', 'label' => 'card_subjects_title', 'icon' => '⚖️', 'category' => 'academic'],
            ['key' => 'assignments', 'route' => 'admin.assignments.index', 'label' => 'card_assignments_title', 'icon' => '🔗', 'category' => 'academic'],
            ['key' => 'schedules', 'route' => 'admin.schedules.index', 'label' => 'card_schedules_title', 'icon' => '🕐', 'category' => 'academic'],
            ['key' => 'attendance_alerts', 'route' => 'admin.attendance-alerts.index', 'label' => 'card_attendance_alerts_title', 'icon' => '🔔', 'category' => 'academic'],
            ['key' => 'guardians', 'route' => 'admin.guardians.index', 'label' => 'card_guardians_title', 'icon' => '👨‍👩‍👧', 'category' => 'academic'],
            ['key' => 'notifications', 'route' => 'admin.notifications.index', 'label' => 'card_notifications_title', 'icon' => '⚠️', 'category' => 'academic'],

            // الشؤون المالية
            ['key' => 'treasury', 'route' => 'admin.treasury.index', 'label' => 'card_treasury_title', 'icon' => '💰', 'category' => 'finance'],
            ['key' => 'tuition_fees', 'route' => 'admin.tuition-fees.index', 'label' => 'card_tuition_fees_title', 'icon' => '🏷️', 'category' => 'finance'],
            ['key' => 'fee_payments', 'route' => 'admin.fee-payments.index', 'label' => 'card_fee_payments_title', 'icon' => '💳', 'category' => 'finance'],
            ['key' => 'late_payments', 'route' => 'admin.late-payments.index', 'label' => 'card_late_payments_title', 'icon' => '⏰', 'category' => 'finance'],
            ['key' => 'withdrawn_students', 'route' => 'admin.withdrawn-students.index', 'label' => 'card_withdrawn_students_title', 'icon' => '🚪', 'category' => 'finance'],
            ['key' => 'partners', 'route' => 'admin.partners.index', 'label' => 'card_partners_title', 'icon' => '🤝', 'category' => 'finance'],

            // الموارد البشرية
            ['key' => 'teachers', 'route' => 'admin.teachers.index', 'label' => 'card_teachers_title', 'icon' => '🧑‍🏫', 'category' => 'hr'],
            ['key' => 'staff_members', 'route' => 'admin.staff-members.index', 'label' => 'card_staff_members_title', 'icon' => '🧑‍💼', 'category' => 'hr'],
            ['key' => 'staff_attendance', 'route' => 'admin.staff-attendance.index', 'label' => 'card_staff_attendance_title', 'icon' => '🕒', 'category' => 'hr'],
            ['key' => 'staff_advances', 'route' => 'admin.staff-advances.index', 'label' => 'staff_advances_page_title', 'icon' => '💸', 'category' => 'hr'],
            ['key' => 'payroll', 'route' => 'admin.payroll.index', 'label' => 'card_payroll_title', 'icon' => '💵', 'category' => 'hr'],
            ['key' => 'teacher_attendance_report', 'route' => 'admin.teacher-attendance-report.index', 'label' => 'card_teacher_attendance_report_title', 'icon' => '📈', 'category' => 'hr'],
        ];
    }

    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }

    public static function byCategory(): array
    {
        $grouped = [];
        foreach (self::all() as $module) {
            $grouped[$module['category']][] = $module;
        }

        return $grouped;
    }

    /**
     * يحول اسم الراوت (مثال: admin.tuition-fees.create) إلى مفتاح الصلاحية (tuition_fees).
     * بعض الروابط المساعدة (AJAX) تُستثنى بتحويلها لصلاحية الوحدة المنطقية التي تخدمها فعلياً.
     */
    public static function permissionKeyForRoute(string $routeName): ?string
    {
        $aliases = [
            'admin.classes.subjects-json' => 'assignments',
        ];

        if (isset($aliases[$routeName])) {
            return $aliases[$routeName];
        }

        $segments = explode('.', $routeName);

        if (count($segments) < 2 || $segments[0] !== 'admin') {
            return null;
        }

        return str_replace('-', '_', $segments[1]);
    }
}
