<?php

namespace App\Support;

use App\Models\ClassSubjectTeacher;

class TeacherAccess
{
    /**
     * معرّف سجل الأستاذ (جدول teachers) للمستخدم الحالي، أو null إن لم يكن أستاذاً.
     */
    public static function teacherId(): ?int
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'teacher') {
            return null;
        }

        return optional($user->teacher)->id;
    }

    /**
     * هل يجب تقييد وصول المستخدم الحالي بالأقسام/المواد التي يدرّسها فقط.
     * يعتمد على الدور وحده (وليس على وجود سجل Teacher) حتى لا يرى حساب أستاذ
     * بلا سجل تدريس مرتبط كل شيء بالخطأ (fail-closed لا fail-open).
     */
    public static function isRestricted(): bool
    {
        $user = auth()->user();

        return (bool) ($user && $user->role === 'teacher');
    }

    public static function assignedClassIds(): array
    {
        $teacherId = self::teacherId();

        if (! $teacherId) {
            return [];
        }

        return ClassSubjectTeacher::where('teacher_id', $teacherId)
            ->pluck('class_id')
            ->unique()
            ->values()
            ->all();
    }

    public static function assignedSubjectIds(?int $classId = null): array
    {
        $teacherId = self::teacherId();

        if (! $teacherId) {
            return [];
        }

        $query = ClassSubjectTeacher::where('teacher_id', $teacherId);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        return $query->pluck('subject_id')->unique()->values()->all();
    }

    /**
     * هل يدرّس الأستاذ الحالي هذه المادة لهذا القسم بالضبط.
     */
    public static function owns(int $classId, int $subjectId): bool
    {
        $teacherId = self::teacherId();

        if (! $teacherId) {
            return false;
        }

        return ClassSubjectTeacher::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    public static function ownsAssignmentId(int $assignmentId): bool
    {
        $teacherId = self::teacherId();

        if (! $teacherId) {
            return false;
        }

        return ClassSubjectTeacher::where('id', $assignmentId)
            ->where('teacher_id', $teacherId)
            ->exists();
    }

    public static function deniedMessage(): string
    {
        return __('messages.teacher_access_denied');
    }
}
