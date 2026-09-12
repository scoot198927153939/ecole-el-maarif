<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with(['grade.enrollment.student', 'grade.assessment.subject', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        Notification::where('is_read', false)->update(['is_read' => true]);

        return view('admin.notifications.index', compact('notifications'));
    }
}