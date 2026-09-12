<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('messages.admin_dashboard_title') }}
        </h2>
        <p class="text-gray-500 text-sm mt-1">{{ __('messages.welcome_user', ['name' => auth()->user()->name]) }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <a href="{{ route('admin.academic-years.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📅</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_academic_years_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_academic_years_desc') }}</p>
                </a>

                <a href="{{ route('admin.classes.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🏫</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_classes_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_classes_desc') }}</p>
                </a>

                <a href="{{ route('admin.students.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🎓</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_students_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_students_desc') }}</p>
                </a>

                <a href="{{ route('admin.students.rosters.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🗂️</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.class_rosters_page_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_class_rosters_desc') }}</p>
                </a>

                <a href="{{ route('admin.enrollments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📝</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_enrollments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_enrollments_desc') }}</p>
                </a>

                <a href="{{ route('admin.assignments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🔗</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_assignments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_assignments_desc') }}</p>
                </a>

                <a href="{{ route('admin.schedules.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🕐</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_schedules_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_schedules_desc') }}</p>
                </a>

                <a href="{{ route('attendance.select') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">✅</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_attendance_take_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_attendance_take_desc') }}</p>
                </a>

                <a href="{{ route('attendance.report.select') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📊</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_attendance_report_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_attendance_report_desc') }}</p>
                </a>

                <a href="{{ route('admin.attendance-alerts.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mb-3 group-hover:bg-red-600 group-hover:text-white transition">🔔</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_attendance_alerts_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_attendance_alerts_desc') }}</p>
                </a>

                <a href="{{ route('assessments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📋</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_assessments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_assessments_desc') }}</p>
                </a>

                <a href="{{ route('gradebook.classes') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📖</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_gradebook_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_gradebook_desc') }}</p>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:bg-amber-600 group-hover:text-white transition">⚠️</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_notifications_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_notifications_desc') }}</p>
                </a>

                <a href="{{ route('lesson-logs.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">📷</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_lesson_logs_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_lesson_logs_desc') }}</p>
                </a>

                <a href="{{ route('admin.subjects.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">⚖️</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_subjects_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_subjects_desc') }}</p>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">👥</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_users_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_users_desc') }}</p>
                </a>

                <a href="{{ route('admin.teachers.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">🧑‍🏫</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_teachers_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_teachers_desc') }}</p>
                </a>

                <a href="{{ route('admin.treasury.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">💰</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_treasury_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_treasury_desc') }}</p>
                </a>

                <a href="{{ route('admin.guardians.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">👨‍👩‍👧</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_guardians_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_guardians_desc') }}</p>
                </a>

                <a href="{{ route('admin.tuition-fees.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">🏷️</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_tuition_fees_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_tuition_fees_desc') }}</p>
                </a>

                <a href="{{ route('admin.fee-payments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">💳</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_fee_payments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_fee_payments_desc') }}</p>
                </a>

                <a href="{{ route('admin.late-payments.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-red-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mb-3 group-hover:bg-red-600 group-hover:text-white transition">⏰</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_late_payments_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_late_payments_desc') }}</p>
                </a>

                <a href="{{ route('admin.staff-members.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">🧑‍💼</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_staff_members_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_staff_members_desc') }}</p>
                </a>

                <a href="{{ route('admin.staff-attendance.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">🕒</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_staff_attendance_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_staff_attendance_desc') }}</p>
                </a>

                <a href="{{ route('admin.payroll.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">💵</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_payroll_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_payroll_desc') }}</p>
                </a>

                <a href="{{ route('admin.teacher-attendance-report.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-amber-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:bg-amber-600 group-hover:text-white transition">📈</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_teacher_attendance_report_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_teacher_attendance_report_desc') }}</p>
                </a>

                <a href="{{ route('admin.partners.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-green-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">🤝</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_partners_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_partners_desc') }}</p>
                </a>

                <a href="{{ route('admin.withdrawn-students.index') }}"
                   class="group bg-white border border-gray-100 shadow-sm rounded-xl p-6 hover:shadow-lg hover:border-red-200 hover:-translate-y-0.5 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mb-3 group-hover:bg-red-600 group-hover:text-white transition">🚪</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-800">{{ __('messages.card_withdrawn_students_title') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('messages.card_withdrawn_students_desc') }}</p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>