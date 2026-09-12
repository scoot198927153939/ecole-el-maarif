<?php

namespace App\Support;

class PermissionPresets
{
    /**
     * قوالب صلاحيات جاهزة لتسهيل إنشاء حسابات المشرفين الشائعة
     * (مراقب، مراقب عام، محاسب، مدير) دون الحاجة لتحديد كل صلاحية يدوياً.
     * كل قالب يبقى مجرد تعبئة أولية للـ checkboxes — يمكن تعديله بعد الاختيار.
     */
    public static function all(): array
    {
        $financeKeys = collect(AdminModules::all())
            ->where('category', 'finance')
            ->pluck('key')
            ->values()
            ->all();

        $academicKeys = collect(AdminModules::all())
            ->where('category', 'academic')
            ->pluck('key')
            ->values()
            ->all();

        $hrKeysExceptPayroll = collect(AdminModules::all())
            ->where('category', 'hr')
            ->pluck('key')
            ->reject(fn ($key) => $key === 'payroll')
            ->values()
            ->all();

        $allTeachingKeys = TeachingModules::keys();

        return [
            'supervisor_basic' => [
                'label' => 'permission_preset_supervisor_basic',
                'admin_modules' => [],
                'teaching_modules' => ['attendance_take', 'attendance_reports', 'reports'],
            ],
            'supervisor_general' => [
                'label' => 'permission_preset_supervisor_general',
                'admin_modules' => ['staff_attendance'],
                'teaching_modules' => ['attendance_take', 'attendance_reports', 'reports', 'gradebook', 'assessments', 'grades'],
            ],
            'accountant' => [
                'label' => 'permission_preset_accountant',
                'admin_modules' => array_values(array_unique(array_merge($financeKeys, ['enrollments', 'staff_attendance', 'payroll']))),
                'teaching_modules' => [],
            ],
            'director' => [
                'label' => 'permission_preset_director',
                'admin_modules' => array_values(array_unique(array_merge($academicKeys, $hrKeysExceptPayroll))),
                'teaching_modules' => $allTeachingKeys,
            ],
        ];
    }
}
