<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\LessonLog;
use App\Models\LessonPhoto;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class LessonLogController extends Controller
{
    public function index(Request $request)
    {
        $lessonLogs = LessonLog::with(['assignment.classRoom', 'assignment.subject', 'assignment.teacher']);

        if (TeacherAccess::isRestricted()) {
            $teacherId = TeacherAccess::teacherId();
            $lessonLogs->whereHas('assignment', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        }

        $lessonLogs = $lessonLogs->orderBy('lesson_date', 'desc')->get();

        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher'])->withCount('lessonLogs');

        if (TeacherAccess::isRestricted()) {
            $assignments->where('teacher_id', TeacherAccess::teacherId());
        }

        $assignments = $assignments->get();

        $classes = $assignments->pluck('classRoom')->unique('id')->sortBy('name')->values();

        $selectedClass = $classes->firstWhere('id', (int) $request->query('class_id'));

        $subjectAssignments = $selectedClass
            ? $assignments->where('class_id', $selectedClass->id)->sortBy(fn ($a) => $a->subject->name)->values()
            : collect();

        return view('lesson-logs.index', compact('lessonLogs', 'classes', 'selectedClass', 'subjectAssignments'));
    }

    public function downloadPdf(ClassSubjectTeacher $assignment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::ownsAssignmentId($assignment->id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        if (! File::exists($this->assignmentPdfPath($assignment))) {
            $this->refreshAssignmentPdf($assignment);
        }

        $path = $this->assignmentPdfPath($assignment);
        abort_unless(File::exists($path), 404);

        $assignment->loadMissing(['classRoom', 'subject']);
        $fileName = str_replace(['/', '\\'], '-', $assignment->classRoom->name.' - '.$assignment->subject->name).'.pdf';

        return response()->download($path, $fileName);
    }

    public function create()
    {
        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher']);

        if (TeacherAccess::isRestricted()) {
            $assignments->where('teacher_id', TeacherAccess::teacherId());
        }

        $assignments = $assignments->get();

        return view('lesson-logs.create', compact('assignments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_subject_teacher_id' => 'required|exists:class_subject_teacher,id',
            'title' => 'required|string|max:255',
            'topic' => 'required|string',
            'lesson_date' => 'required|date',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::ownsAssignmentId((int) $validated['class_subject_teacher_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $lessonLog = LessonLog::create([
            'class_subject_teacher_id' => $validated['class_subject_teacher_id'],
            'title' => $validated['title'],
            'topic' => $validated['topic'],
            'lesson_date' => $validated['lesson_date'],
        ]);

        $this->storeUploadedPhotos($lessonLog, $request->file('photos'));
        $this->regeneratePdf($lessonLog);
        $this->refreshAssignmentPdf($lessonLog->assignment);

        return redirect()->route('lesson-logs.index')
            ->with('success', __('messages.flash_lesson_log_created'));
    }

    public function edit(LessonLog $lessonLog)
    {
        $lessonLog->loadMissing('assignment');

        if (TeacherAccess::isRestricted() && optional($lessonLog->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $lessonLog->load('photos');

        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher']);

        if (TeacherAccess::isRestricted()) {
            $assignments->where('teacher_id', TeacherAccess::teacherId());
        }

        $assignments = $assignments->get();

        return view('lesson-logs.edit', compact('lessonLog', 'assignments'));
    }

    public function update(Request $request, LessonLog $lessonLog)
    {
        $lessonLog->loadMissing('assignment');

        if (TeacherAccess::isRestricted() && optional($lessonLog->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated = $request->validate([
            'class_subject_teacher_id' => 'required|exists:class_subject_teacher,id',
            'title' => 'required|string|max:255',
            'topic' => 'required|string',
            'lesson_date' => 'required|date',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::ownsAssignmentId((int) $validated['class_subject_teacher_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $previousAssignment = $lessonLog->assignment;

        $lessonLog->update([
            'class_subject_teacher_id' => $validated['class_subject_teacher_id'],
            'title' => $validated['title'],
            'topic' => $validated['topic'],
            'lesson_date' => $validated['lesson_date'],
        ]);

        if ($request->hasFile('photos')) {
            $this->storeUploadedPhotos($lessonLog, $request->file('photos'));
        }

        $this->regeneratePdf($lessonLog);
        $this->refreshAssignmentPdf($previousAssignment);

        if ($previousAssignment->id !== $lessonLog->class_subject_teacher_id) {
            $this->refreshAssignmentPdf($lessonLog->assignment);
        }

        return redirect()->route('lesson-logs.index')
            ->with('success', __('messages.flash_lesson_log_updated'));
    }

    public function destroyPhoto(LessonPhoto $photo)
    {
        $lessonLog = $photo->lessonLog;
        $lessonLog->loadMissing('assignment');

        if (TeacherAccess::isRestricted() && optional($lessonLog->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $fullPath = storage_path('app/public/'.$photo->photo_path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $photo->delete();

        $this->regeneratePdf($lessonLog);
        $this->refreshAssignmentPdf($lessonLog->assignment);

        return back()->with('success', __('messages.flash_lesson_log_photo_deleted'));
    }

    public function destroy(LessonLog $lessonLog)
    {
        $lessonLog->loadMissing('assignment');

        if (TeacherAccess::isRestricted() && optional($lessonLog->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        foreach ($lessonLog->photos as $photo) {
            $fullPath = storage_path('app/public/'.$photo->photo_path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        if ($lessonLog->pdf_path) {
            $pdfPath = storage_path('app/public/'.$lessonLog->pdf_path);
            if (File::exists($pdfPath)) {
                File::delete($pdfPath);
            }
        }

        $assignment = $lessonLog->assignment;

        $lessonLog->delete();

        $this->refreshAssignmentPdf($assignment);

        return redirect()->route('lesson-logs.index')
            ->with('success', __('messages.flash_lesson_log_deleted'));
    }

    private function assignmentPdfPath(ClassSubjectTeacher $assignment): string
    {
        return storage_path('app/public/lesson_pdfs/subject_'.$assignment->id.'.pdf');
    }

    private function refreshAssignmentPdf(ClassSubjectTeacher $assignment): void
    {
        $path = $this->assignmentPdfPath($assignment);

        $lessons = $assignment->lessonLogs()->with('photos')->orderBy('lesson_date')->orderBy('id')->get();

        if ($lessons->isEmpty()) {
            if (File::exists($path)) {
                File::delete($path);
            }

            return;
        }

        $assignment->loadMissing(['classRoom', 'subject', 'teacher']);

        $isArabic = app()->getLocale() === 'ar';
        $direction = $isArabic ? 'rtl' : 'ltr';
        $textAlign = $isArabic ? 'right' : 'left';

        $style = '
            <style>
                body { font-family: dejavusans; direction: '.$direction.'; padding: 20px; }
                h1 { text-align: center; font-size: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                td, th { border: 1px solid #333; padding: 10px; text-align: '.$textAlign.'; }
                th { background-color: #f0f0f0; width: 30%; }
            </style>
        ';

        $mpdfConfig = [
            'default_font' => 'dejavusans',
            'directionality' => $direction,
        ];

        if ($isArabic) {
            $mpdfConfig['mode'] = 'ar';
        }

        $mpdf = new Mpdf($mpdfConfig);

        $mpdf->WriteHTML($style.'
            <h1>'.__('messages.pdf_subject_lessons_title').'</h1>
            <table>
                <tr>
                    <th>'.__('messages.class').'</th>
                    <td>'.$assignment->classRoom->name.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.subject').'</th>
                    <td>'.$assignment->subject->name.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.teacher').'</th>
                    <td>'.$assignment->teacher->first_name.' '.$assignment->teacher->last_name.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.pdf_lessons_count_label').'</th>
                    <td>'.$lessons->count().'</td>
                </tr>
            </table>
        ');

        foreach ($lessons as $index => $lesson) {
            if ($index > 0) {
                $mpdf->AddPage();
            }

            $mpdf->WriteHTML($style.'
                <table>
                    <tr>
                        <th>'.__('messages.pdf_lesson_title_label').'</th>
                        <td>'.$lesson->title.'</td>
                    </tr>
                    <tr>
                        <th>'.__('messages.pdf_lesson_topic_label').'</th>
                        <td>'.$lesson->topic.'</td>
                    </tr>
                    <tr>
                        <th>'.__('messages.pdf_lesson_date_label').'</th>
                        <td><span dir="ltr">'.$lesson->lesson_date->format('Y-m-d').'</span></td>
                    </tr>
                </table>
            ');

            foreach ($lesson->photos as $photo) {
                $fullPath = storage_path('app/public/'.$photo->photo_path);

                if (File::exists($fullPath)) {
                    $imageData = base64_encode(file_get_contents($fullPath));
                    $mimeType = File::mimeType($fullPath);

                    $mpdf->AddPage();
                    $mpdf->WriteHTML('<img src="data:'.$mimeType.';base64,'.$imageData.'" style="width: 100%;">');
                }
            }
        }

        if (! File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        $mpdf->Output($path, 'F');
    }

    private function storeUploadedPhotos(LessonLog $lessonLog, array $photos): void
    {
        foreach ($photos as $photo) {
            $fileName = 'photo_'.$lessonLog->id.'_'.Str::random(8).'.'.$photo->getClientOriginalExtension();
            $relativePath = 'lesson_photos/'.$fileName;

            $photo->storeAs('lesson_photos', $fileName, 'public');

            LessonPhoto::create([
                'lesson_log_id' => $lessonLog->id,
                'photo_path' => $relativePath,
            ]);
        }
    }

    private function regeneratePdf(LessonLog $lessonLog): void
    {
        $lessonLog->load(['assignment.subject', 'photos']);

        // حذف ملف الـ PDF القديم إن وجد
        if ($lessonLog->pdf_path) {
            $oldPath = storage_path('app/public/'.$lessonLog->pdf_path);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $isArabic = app()->getLocale() === 'ar';

        $mpdfConfig = [
            'default_font' => 'dejavusans',
            'directionality' => $isArabic ? 'rtl' : 'ltr',
        ];

        if ($isArabic) {
            $mpdfConfig['mode'] = 'ar';
        }

        $mpdf = new Mpdf($mpdfConfig);

        $direction = $isArabic ? 'rtl' : 'ltr';
        $textAlign = $isArabic ? 'right' : 'left';

        $headerHtml = '
            <style>
                body { font-family: dejavusans; direction: '.$direction.'; padding: 20px; }
                h1 { text-align: center; font-size: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                td, th { border: 1px solid #333; padding: 10px; text-align: '.$textAlign.'; }
                th { background-color: #f0f0f0; width: 30%; }
            </style>
            <h1>'.__('messages.pdf_lesson_log_title').'</h1>
            <table>
                <tr>
                    <th>'.__('messages.subject').'</th>
                    <td>'.$lessonLog->assignment->subject->name.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.pdf_lesson_title_label').'</th>
                    <td>'.$lessonLog->title.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.pdf_lesson_topic_label').'</th>
                    <td>'.$lessonLog->topic.'</td>
                </tr>
                <tr>
                    <th>'.__('messages.pdf_lesson_date_label').'</th>
                    <td><span dir="ltr">'.$lessonLog->lesson_date->format('Y-m-d').'</span></td>
                </tr>
            </table>
        ';

        $mpdf->WriteHTML($headerHtml);

        foreach ($lessonLog->photos as $photo) {
            $fullPath = storage_path('app/public/'.$photo->photo_path);

            if (File::exists($fullPath)) {
                $imageData = base64_encode(file_get_contents($fullPath));
                $mimeType = File::mimeType($fullPath);

                $mpdf->AddPage();
                $mpdf->WriteHTML('<img src="data:'.$mimeType.';base64,'.$imageData.'" style="width: 100%;">');
            }
        }

        $fileName = 'lesson_'.$lessonLog->id.'_'.Str::random(8).'.pdf';
        $relativePath = 'lesson_pdfs/'.$fileName;
        $fullPath = storage_path('app/public/'.$relativePath);

        if (! File::exists(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }

        $mpdf->Output($fullPath, 'F');

        $lessonLog->update(['pdf_path' => $relativePath]);
    }
}