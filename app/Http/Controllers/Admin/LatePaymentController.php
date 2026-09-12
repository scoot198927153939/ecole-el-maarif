<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;

class LatePaymentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student.guardian', 'classRoom'])
            ->get()
            ->filter(fn ($e) => $e->remainingAmount() > 0);

        // نرتبهم حسب اسم ولي الأمر (اللي ما عنده ولي أمر مربوط يظهر آخر شي)
        $grouped = $enrollments->sortBy(function ($e) {
            return $e->student->guardian?->name ?? 'ﻯ';
        })->groupBy(function ($e) {
            return $e->student->guardian_id ?? 'بدون ولي أمر مربوط';
        });

        return view('admin.late-payments.index', compact('grouped'));
    }
}