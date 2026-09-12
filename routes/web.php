<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeBookController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LessonLogController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\Admin\AttendanceAlertController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\Admin\TreasuryController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\TuitionFeeController;
use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\LatePaymentController;
use App\Http\Controllers\Admin\StaffMemberController;
use App\Http\Controllers\Admin\StaffAttendanceController;
use App\Http\Controllers\Admin\PayrollController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return view('admin.dashboard');
    } elseif ($user->role === 'supervisor') {
        $grantedModules = collect(\App\Support\AdminModules::all())
            ->filter(fn ($module) => $user->hasAdminPermission($module['key']))
            ->values();

        return view('supervisor.dashboard', compact('grantedModules'));
    } elseif ($user->role === 'guardian') {
        return redirect()->route('parent.dashboard');
    } else {
        return view('teacher.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::prefix('parent')->name('parent.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Guardian\AuthController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Guardian\AuthController::class, 'store']);
    });

    Route::middleware(['auth', 'guardian_only'])->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Guardian\AuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [\App\Http\Controllers\Guardian\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students/{student}', [\App\Http\Controllers\Guardian\DashboardController::class, 'showStudent'])->name('students.show');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/send-reset-link', [UserController::class, 'sendResetLink'])->name('users.send-reset-link');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// كل صفحات الأدمن ما عدا إدارة المستخدمين: مفتوحة للأدمن دائماً، ولأي مستخدم آخر مُنحت له صلاحية الوحدة تحديداً.
Route::middleware(['auth', 'admin_access'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('academic-years', AcademicYearController::class)
        ->except(['show']);
    Route::resource('classes', ClassRoomController::class)
        ->except(['show'])
        ->parameters(['classes' => 'class']);
    Route::resource('students', StudentController::class)
        ->except(['show']);

Route::get('/students/{student}/card-pdf', [\App\Http\Controllers\PdfController::class, 'studentCard'])->name('students.card.pdf');

Route::get('/students-rosters', [\App\Http\Controllers\Admin\ClassRosterController::class, 'index'])->name('students.rosters.index');
Route::get('/students-rosters/{class}', [\App\Http\Controllers\Admin\ClassRosterController::class, 'show'])->name('students.rosters.show');
Route::get('/students-rosters/{class}/export/csv', [\App\Http\Controllers\Admin\ClassRosterController::class, 'exportCsv'])->name('students.rosters.csv');
Route::get('/students-rosters/{class}/export/pdf', [\App\Http\Controllers\PdfController::class, 'classRoster'])->name('students.rosters.pdf');


    Route::resource('enrollments', EnrollmentController::class)
        ->except(['show']);
    Route::resource('subjects', SubjectController::class)
        ->except(['show']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::resource('assignments', AssignmentController::class)->except(['show']);
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class)->only(['index', 'edit', 'update']);
    Route::get('/classes/{class}/subjects-json', [AssignmentController::class, 'subjectsForClass'])->name('classes.subjects-json');

    Route::resource('schedules', ScheduleController::class)->except(['show']);

    Route::get('/attendance-alerts', [AttendanceAlertController::class, 'index'])->name('attendance-alerts.index');

    Route::get('/treasury', [TreasuryController::class, 'index'])->name('treasury.index');
Route::get('/treasury/sources', [TreasuryController::class, 'sources'])->name('treasury.sources');
Route::post('/treasury/sources', [TreasuryController::class, 'storeSource'])->name('treasury.sources.store');
Route::delete('/treasury/sources/{source}', [TreasuryController::class, 'destroySource'])->name('treasury.sources.destroy');
Route::get('/treasury/sources/{source}/transactions', [TreasuryController::class, 'transactions'])->name('treasury.transactions');
Route::post('/treasury/sources/{source}/transactions', [TreasuryController::class, 'storeTransaction'])->name('treasury.transactions.store');

Route::resource('guardians', GuardianController::class)->except(['show']);
Route::post('/guardians/{guardian}/regenerate-password', [GuardianController::class, 'regeneratePassword'])->name('guardians.regenerate-password');
Route::post('/guardians/{guardian}/create-account', [GuardianController::class, 'createAccount'])->name('guardians.create-account');

Route::resource('tuition-fees', TuitionFeeController::class)->except(['show']);
Route::get('/fee-payments', [FeePaymentController::class, 'index'])->name('fee-payments.index');
Route::get('/fee-payments/{enrollment}', [FeePaymentController::class, 'show'])->name('fee-payments.show');
Route::post('/fee-payments/{enrollment}', [FeePaymentController::class, 'store'])->name('fee-payments.store');

Route::post('/fee-payments/{enrollment}/discount', [FeePaymentController::class, 'updateDiscount'])->name('fee-payments.discount');

Route::get('/late-payments', [LatePaymentController::class, 'index'])->name('late-payments.index');

Route::resource('staff-members', StaffMemberController::class)->except(['show']);
Route::get('/staff-attendance', [StaffAttendanceController::class, 'index'])->name('staff-attendance.index');
Route::post('/staff-attendance', [StaffAttendanceController::class, 'store'])->name('staff-attendance.store');
Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::post('/partners/{partner}/withdrawals', [\App\Http\Controllers\Admin\PartnerController::class, 'storeWithdrawal'])->name('partners.withdrawals.store');
Route::get('/withdrawn-students', [\App\Http\Controllers\Admin\WithdrawnStudentController::class, 'index'])->name('withdrawn-students.index');
Route::get('/withdrawn-students/{enrollment}', [\App\Http\Controllers\Admin\WithdrawnStudentController::class, 'show'])->name('withdrawn-students.show');
Route::post('/withdrawn-students/{enrollment}/refunds', [\App\Http\Controllers\Admin\WithdrawnStudentController::class, 'storeRefund'])->name('withdrawn-students.refunds.store');
Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
Route::get('/teacher-attendance-report', [\App\Http\Controllers\Admin\TeacherAttendanceReportController::class, 'index'])->name('teacher-attendance-report.index');
Route::resource('staff-advances', \App\Http\Controllers\Admin\StaffAdvanceController::class)->only(['index', 'create', 'store', 'destroy']);
Route::get('/payroll/{type}/{id}', [PayrollController::class, 'show'])->name('payroll.show');
Route::post('/staff-advances/{staffAdvance}/deductions', [PayrollController::class, 'storeDeduction'])->name('payroll.deductions.store');
Route::post('/payroll/payment-choice', [PayrollController::class, 'storePaymentChoice'])->name('payroll.payment-choice.store');
});


Route::middleware(['auth', 'admin_or_teacher'])->group(function () {
    Route::get('/gradebook', [GradeBookController::class, 'classes'])->name('gradebook.classes');
    Route::get('/gradebook/{class}', [GradeBookController::class, 'subjects'])->name('gradebook.subjects');
    Route::get('/gradebook/{class}/{subject}', [GradeBookController::class, 'grid'])->name('gradebook.grid');
    Route::post('/gradebook/{class}/{subject}', [GradeBookController::class, 'store'])->name('gradebook.store');

    Route::get('/assessments/bulk-create', [AssessmentController::class, 'bulkCreate'])->name('assessments.bulk-create');
    Route::post('/assessments/bulk-store', [AssessmentController::class, 'bulkStore'])->name('assessments.bulk-store');
    Route::resource('assessments', AssessmentController::class)->except(['show']);

    Route::get('/assessments/{assessment}/grades', [GradeController::class, 'index'])
        ->name('grades.index');
    Route::post('/assessments/{assessment}/grades', [GradeController::class, 'store'])
        ->name('grades.store');

    Route::get('/enrollments/{enrollment}/report', [ReportController::class, 'show'])
        ->name('reports.show');

    Route::get('/grades/{grade}/pdf', [PdfController::class, 'singleGrade'])
        ->name('grades.pdf');
    Route::get('/assessments/{assessment}/pdf', [PdfController::class, 'assessmentGrades'])
        ->name('assessments.pdf');
    Route::get('/enrollments/{enrollment}/term/{term}/pdf', [PdfController::class, 'termReport'])
        ->name('reports.term.pdf');

    Route::get('/lesson-logs', [LessonLogController::class, 'index'])->name('lesson-logs.index');
    Route::get('/lesson-logs/create', [LessonLogController::class, 'create'])->name('lesson-logs.create');
    Route::post('/lesson-logs', [LessonLogController::class, 'store'])->name('lesson-logs.store');
    Route::get('/lesson-logs/{lessonLog}/edit', [LessonLogController::class, 'edit'])->name('lesson-logs.edit');
    Route::put('/lesson-logs/{lessonLog}', [LessonLogController::class, 'update'])->name('lesson-logs.update');
    Route::delete('/lesson-logs/{lessonLog}', [LessonLogController::class, 'destroy'])->name('lesson-logs.destroy');
    Route::delete('/lesson-photos/{photo}', [LessonLogController::class, 'destroyPhoto'])->name('lesson-logs.photos.destroy');

    Route::get('/enrollments/{enrollment}/term1-pdf', [PdfController::class, 'term1Report'])->name('reports.term1.pdf');
    Route::get('/enrollments/{enrollment}/term2-pdf', [PdfController::class, 'term2Report'])->name('reports.term2.pdf');
    Route::get('/enrollments/{enrollment}/term3-pdf', [PdfController::class, 'term3Report'])->name('reports.term3.pdf');

    Route::get('/classes/{class}/term1-pdf', [PdfController::class, 'classTerm1Report'])->name('reports.class.term1.pdf');
    Route::get('/classes/{class}/term2-pdf', [PdfController::class, 'classTerm2Report'])->name('reports.class.term2.pdf');
    Route::get('/classes/{class}/term3-pdf', [PdfController::class, 'classTerm3Report'])->name('reports.class.term3.pdf');

    Route::get('/attendance', [AttendanceController::class, 'select'])->name('attendance.select');
    Route::get('/attendance/schedules', [AttendanceController::class, 'schedulesForDay'])->name('attendance.schedules');
    Route::get('/attendance/{schedule}/take', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/{schedule}/take', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/attendance-report', [AttendanceReportController::class, 'selectClass'])->name('attendance.report.select');
    Route::get('/attendance-report/{class}/daily', [AttendanceReportController::class, 'daily'])->name('attendance.report.daily');
    Route::get('/attendance-report/{class}/monthly', [AttendanceReportController::class, 'monthly'])->name('attendance.report.monthly');

    Route::get('/students/{enrollment}/profile', [StudentProfileController::class, 'show'])->name('students.profile');
});