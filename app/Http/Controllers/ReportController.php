<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'laporan-keuangan');
        
        $types = [
            'laporan-keuangan' => 'Laporan Keuangan',
            'laporan-tahunan' => 'Laporan Tahunan', 
            'laporan-pengaduan' => 'Laporan Pengaduan',
        ];

        // Handle complaints (laporan-pengaduan) differently
        if ($type === 'laporan-pengaduan') {
            $baseQuery = Complaint::query()
                ->when($request->search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('subject', 'like', "%{$search}%")
                          ->orWhere('message', 'like', "%{$search}%");
                    });
                })
                ->when($request->status, function ($query, $status) {
                    return $query->status($status);
                });

            $totalComplaints = $baseQuery->count();
            
            $complaints = $baseQuery->with('respondedBy')
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return Inertia::render('Report/ComplaintIndex', [
                'complaints' => $complaints,
                'totalComplaints' => $totalComplaints,
                'filters' => $request->only(['search', 'status', 'type']),
                'statuses' => Complaint::getStatuses(),
                'types' => $types,
                'currentType' => $type,
                'currentTypeName' => $types[$type] ?? $type,
            ]);
        }

        // Handle regular reports
        $reports = Report::ofType($type)
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title_id', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhere('year', 'like', "%{$search}%");
                });
            })
            ->when($request->year, function ($query, $year) {
                return $query->forYear($year);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $years = Report::ofType($type)
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $periods = [
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulan',
            'yearly' => 'Tahunan',
        ];

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $quarters = [
            1 => 'Triwulan I',
            2 => 'Triwulan II',
            3 => 'Triwulan III', 
            4 => 'Triwulan IV'
        ];

        return Inertia::render('Report/ReportIndex', [
            'reports' => $reports,
            'filters' => $request->only(['search', 'year', 'type']),
            'years' => $years,
            'types' => $types,
            'periods' => $periods,
            'months' => $months,
            'quarters' => $quarters,
            'currentType' => $type,
            'currentTypeName' => $types[$type] ?? $type,
        ]);
    }


    public function store(Request $request)
    {
        // Check if upload failed due to PHP limits
        if (empty($_FILES) && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $displayMaxSize = ini_get('post_max_size') ?: '2MB';
            return back()->withErrors([
                'file' => "File terlalu besar. Maksimal ukuran yang diizinkan server adalah {$displayMaxSize}. Silakan kompres file atau hubungi administrator."
            ]);
        }

        $validated = $request->validate([
            'type' => 'required|in:laporan-keuangan,laporan-tahunan,laporan-pengaduan',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'year' => 'required|integer|min:1900|max:2100',
            'period' => 'required|in:monthly,quarterly,yearly',
            'month' => 'nullable|integer|min:1|max:12',
            'quarter' => 'nullable|integer|min:1|max:4',
            'file' => 'required|file|mimes:pdf|max:10240', // Required for create, Max 10MB
            'is_published' => 'boolean',
        ], [
            'file.required' => 'File PDF wajib diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'File terlalu besar. Maksimal 10MB.',
        ]);

        // Validate period-specific fields
        if ($validated['period'] === 'monthly' && !$request->month) {
            return back()->withErrors(['month' => 'Month is required for monthly reports.']);
        }

        if ($validated['period'] === 'quarterly' && !$request->quarter) {
            return back()->withErrors(['quarter' => 'Quarter is required for quarterly reports.']);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($validated['title_id']) . '.pdf';
            $filePath = $file->storeAs('reports/' . $validated['type'], $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $validated['published_at'] = $validated['is_published'] ? now() : null;

        Report::create($validated);

        return redirect()
            ->route('reports.index', ['type' => $validated['type']])
            ->with('success', 'Report berhasil ditambahkan.');
    }


    public function update(Request $request, Report $report)
    {
        // Check if upload failed due to PHP limits (only if trying to upload)
        if ($request->hasFile('file') && empty($_FILES) && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $displayMaxSize = ini_get('post_max_size') ?: '2MB';
            return back()->withErrors([
                'file' => "File terlalu besar. Maksimal ukuran yang diizinkan server adalah {$displayMaxSize}. Silakan kompres file atau hubungi administrator."
            ]);
        }

        $validated = $request->validate([
            'type' => 'required|in:laporan-keuangan,laporan-tahunan,laporan-pengaduan',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'year' => 'required|integer|min:1900|max:2100',
            'period' => 'required|in:monthly,quarterly,yearly',
            'month' => 'nullable|integer|min:1|max:12',
            'quarter' => 'nullable|integer|min:1|max:4',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'is_published' => 'boolean',
        ], [
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'File terlalu besar. Maksimal 10MB.',
        ]);

        // Validate period-specific fields
        if ($validated['period'] === 'monthly' && !$request->month) {
            return back()->withErrors(['month' => 'Month is required for monthly reports.']);
        }

        if ($validated['period'] === 'quarterly' && !$request->quarter) {
            return back()->withErrors(['quarter' => 'Quarter is required for quarterly reports.']);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                Storage::disk('public')->delete($report->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($validated['title_id']) . '.pdf';
            $filePath = $file->storeAs('reports/' . $validated['type'], $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $validated['published_at'] = $validated['is_published'] ? ($report->published_at ?? now()) : null;

        $report->update($validated);

        return redirect()
            ->route('reports.index', ['type' => $validated['type']])
            ->with('success', 'Report berhasil diperbarui.');
    }

    public function destroy(Report $report)
    {
        $type = $report->type;
        $report->delete();

        return redirect()
            ->route('reports.index', ['type' => $type])
            ->with('success', 'Report berhasil dihapus.');
    }

    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($report->file_path, $report->file_name);
    }

    public function respondToComplaint(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string',
            'status' => 'required|in:pending,in_review,resolved,rejected',
        ]);

        $complaint->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('reports.index', ['type' => 'laporan-pengaduan'])
            ->with('success', 'Respon berhasil dikirim.');
    }
}