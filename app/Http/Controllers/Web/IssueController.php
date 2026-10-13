<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class IssueController extends Controller
{
    private array $months = [
        1 => 'Ocak',
        2 => 'Şubat',
        3 => 'Mart',
        4 => 'Nisan',
        5 => 'Mayıs',
        6 => 'Haziran',
        7 => 'Temmuz',
        8 => 'Ağustos',
        9 => 'Eylül',
        10 => 'Ekim',
        11 => 'Kasım',
        12 => 'Aralık',
    ];

    public function index(Request $request): View
    {
        $query = Issue::query()
            ->with('coverImage')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at');

        $selectedYear = $request->query('year');
        $selectedMonth = $request->query('month');

        if ($selectedYear) {
            $query->where('year', (int) $selectedYear);
        }

        if ($selectedMonth) {
            $query->where('month', (int) $selectedMonth);
        }

        $issues = $query->paginate(9)->withQueryString();
        $years = Issue::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');

        return view('sayilar.index', [
            'issues' => $issues,
            'years' => $years,
            'months' => $this->months,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
        ]);
    }

    public function latest(): RedirectResponse
    {
        $latestIssue = Issue::query()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->first();

        if (!$latestIssue) {
            return redirect()->route('issues.index');
        }

        if ($latestIssue->pdf_url) {
            return redirect()->route('issues.read', [
                'issue' => $latestIssue,
                'v' => $latestIssue->updated_at?->timestamp ?? $latestIssue->id,
            ]);
        }

        return redirect()->route('issues.show', $latestIssue);
    }

    public function read(Issue $issue)
    {
        abort_unless($issue->pdf_path, 404);
        abort_unless(Storage::disk('public')->exists($issue->pdf_path), 404);

        $this->incrementCounterIfAvailable($issue, 'read_count');

        $response = response()->file(Storage::disk('public')->path($issue->pdf_path), [
            'Content-Type' => 'application/pdf',
        ]);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function download(Issue $issue)
    {
        abort_unless($issue->pdf_path, 404);
        abort_unless(Storage::disk('public')->exists($issue->pdf_path), 404);

        $this->incrementCounterIfAvailable($issue, 'download_count');

        return Storage::disk('public')->download(
            $issue->pdf_path,
            $issue->pdf_original_name ?: $issue->slug.'.pdf',
        );
    }

    public function show(Issue $issue): View
    {
        $issue->loadMissing('coverImage');

        return view('sayilar.show', [
            'issue' => $issue,
        ]);
    }

    private function incrementCounterIfAvailable(Issue $issue, string $column): void
    {
        try {
            if (Schema::hasColumn($issue->getTable(), $column)) {
                $issue->newQuery()->whereKey($issue->getKey())->increment($column);
            }
        } catch (Throwable) {
        }
    }
}
