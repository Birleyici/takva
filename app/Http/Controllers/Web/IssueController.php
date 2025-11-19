<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            ->orderByDesc('month');

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

    public function show(Issue $issue): View
    {
        $issue->loadMissing('coverImage');

        return view('sayilar.show', [
            'issue' => $issue,
        ]);
    }
}
