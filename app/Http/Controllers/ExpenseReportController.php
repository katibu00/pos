<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ExpenseRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('expense.reports', compact('branches'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:overview,by_category,by_branch,top_expenses,trend_analysis,monthly_summary,yearly_summary',
            'time_frame' => 'required|in:today,yesterday,last_7_days,last_30_days,single_date,date_range',
            'single_date' => [
                'required_if:time_frame,single_date',
                'nullable',
                'date',
            ],
            'start_date' => [
                'required_if:time_frame,date_range',
                'nullable',
                'date',
            ],
            'end_date' => [
                'required_if:time_frame,date_range',
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Custom date validation to provide detailed error messages
        if ($request->time_frame === 'single_date' && !$request->single_date) {
            return response()->json([
                'errors' => ['single_date' => ['The single date is required when the time frame is set to single_date.']],
            ], 422);
        }

        if ($request->time_frame === 'date_range') {
            if (!$request->start_date || !$request->end_date) {
                return response()->json([
                    'errors' => ['start_date' => ['The start date and end date are required when the time frame is set to date_range.']],
                ], 422);
            }

            if (strtotime($request->end_date) < strtotime($request->start_date)) {
                return response()->json([
                    'errors' => ['end_date' => ['The end date must be a date after or equal to start date.']],
                ], 422);
            }
        }

        $dateRange = $this->getDateRange($request->time_frame, $request->single_date, $request->start_date, $request->end_date);
        $query = ExpenseRecord::whereBetween('created_at', $dateRange);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $expenses = $query->get();

        switch ($request->report_type) {
            case 'overview':
                return $this->generateOverviewReport($expenses, $dateRange);
            case 'by_category':
                return $this->generateByCategoryReport($expenses, $dateRange);
            case 'by_branch':
                return $this->generateByBranchReport($expenses, $dateRange);
            case 'top_expenses':
                return $this->generateTopExpensesReport($expenses, $dateRange);
            case 'trend_analysis':
                return $this->generateTrendAnalysisReport($expenses, $dateRange);
            case 'monthly_summary':
                return $this->generateMonthlySummaryReport($expenses, $dateRange);
            case 'yearly_summary':
                return $this->generateYearlySummaryReport($expenses, $dateRange);
        }
    }

    private function getDateRange($timeFrame, $singleDate, $startDate, $endDate)
    {
        switch ($timeFrame) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'yesterday':
                return [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()];
            case 'last_7_days':
                return [now()->subDays(6)->startOfDay(), now()->endOfDay()];
            case 'last_30_days':
                return [now()->subDays(29)->startOfDay(), now()->endOfDay()];
            case 'single_date':
                return [Carbon::parse($singleDate)->startOfDay(), Carbon::parse($singleDate)->endOfDay()];
            case 'date_range':
                return [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }
    }

    private function generateOverviewReport($expenses, $dateRange)
    {
        $totalExpenses = $expenses->sum('amount');
        $daysInRange = $dateRange[0]->diffInDays($dateRange[1]) + 1;
        $avgDailyExpense = $daysInRange > 0 ? $totalExpenses / $daysInRange : 0;

        $categoriesData = $expenses->groupBy('category.name')->map->sum('amount')->sort()->reverse();

        $topCategory = $categoriesData->keys()->first();
        $topCategoryPercentage = $totalExpenses > 0 ? number_format(($categoriesData->first() / $totalExpenses) * 100, 1) : 0;

        $chartData = [
            'type' => 'pie',
            'data' => [
                'labels' => $categoriesData->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $categoriesData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($categoriesData)),
                    ],
                ],
            ],
            'title' => 'Expense Distribution by Category',
        ];

        $trendData = $expenses->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        })->map->sum('amount');

        $trendChartData = [
            'type' => 'line',
            'data' => [
                'labels' => $trendData->keys()->toArray(),
                'datasets' => [
                    [
                        'label' => 'Daily Expenses',
                        'data' => $trendData->values()->toArray(),
                        'borderColor' => '#36A2EB',
                        'fill' => false,
                    ],
                ],
            ],
            'title' => 'Daily Expense Trend',
        ];

        $analysis = "
        <h4>Expense Overview Analysis</h4>
        <p>During the selected period from {$dateRange[0]->format('M d, Y')} to {$dateRange[1]->format('M d, Y')}, total expenses amounted to ₦" . number_format($totalExpenses, 2) . ".</p>
        <p>The average daily expense was ₦" . number_format($avgDailyExpense, 2) . ", calculated over {$daysInRange} days.</p>";

        if ($totalExpenses > 0 && $topCategory) {
            $analysis .= "<p>The top expense category was <strong>{$topCategory}</strong>, accounting for {$topCategoryPercentage}% of total expenses.</p>";
        } else {
            $analysis .= "<p>There were no expenses recorded during this period.</p>";
        }

        $analysis .= "
        <h4>Recommendations</h4>
        <ul>
            <li>Focus on reducing expenses in the {$topCategory} category, as it represents the largest portion of your spending.</li>
            <li>Review your daily expenses and identify any days with unusually high spending to understand the causes.</li>
            <li>Consider setting a daily budget of ₦" . number_format($avgDailyExpense * 0.9, 2) . " (10% less than your current average) to gradually reduce overall expenses.</li>
        </ul>";

        return [
            'title' => 'Expense Overview Report',
            'chartData' => $chartData,
            'trendChartData' => $trendChartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($categoriesData, ['Category', 'Amount']),
        ];
    }



    private function generateByCategoryReport($expenses, $dateRange)
    {
        $categoriesData = $expenses->groupBy('category.name')->map->sum('amount')->sort()->reverse();
    
        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $categoriesData->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $categoriesData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($categoriesData)),
                    ],
                ],
            ],
            'options' => [
                'indexAxis' => 'y',
            ],
            'title' => 'Expenses by Category',
        ];
    
        $totalExpenses = $categoriesData->sum();
        $topCategory = $categoriesData->keys()->first();
        $topCategoryPercentage = $totalExpenses > 0 ? number_format(($categoriesData->first() / $totalExpenses) * 100, 1) : 0;
    
        $analysis = "
        <h4>Category Breakdown Analysis</h4>
        <p>From {$dateRange[0]->format('M d, Y')} to {$dateRange[1]->format('M d, Y')}, expenses were distributed across " . count($categoriesData) . " categories.</p>
        <p>The top spending category was <strong>{$topCategory}</strong>, accounting for {$topCategoryPercentage}% of total expenses.</p>
        <p>The bottom " . min(3, count($categoriesData)) . " categories with the least expenses are: " . implode(', ', $categoriesData->keys()->take(-3)->reverse()->toArray()) . ".</p>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Analyze the reasons behind high spending in the {$topCategory} category and identify potential areas for cost-cutting.</li>
            <li>Consider reallocating funds from high-expense categories to lower ones if possible.</li>
            <li>Set category-specific budgets to better control spending across all expense types.</li>
        </ul>";
    
        return [
            'title' => 'Expenses by Category Report',
            'chartData' => $chartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($categoriesData, ['Category', 'Amount']),
        ];
    }

    private function generateByBranchReport($expenses, $dateRange)
    {
        $branchData = $expenses->groupBy('branch.name')->map->sum('amount')->sort()->reverse();
    
        $pieChartData = [
            'type' => 'pie',
            'data' => [
                'labels' => $branchData->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $branchData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($branchData)),
                    ],
                ],
            ],
            'title' => 'Expense Distribution by Branch',
        ];
    
        $barChartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $branchData->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $branchData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($branchData)),
                    ],
                ],
            ],
            'title' => 'Expense Comparison by Branch',
        ];
    
        $totalExpenses = $branchData->sum();
        $topBranch = $branchData->keys()->first();
        $topBranchPercentage = $totalExpenses > 0 ? number_format(($branchData->first() / $totalExpenses) * 100, 1) : 0;
    
        $analysis = "
        <h4>Branch Expense Analysis</h4>
        <p>From {$dateRange[0]->format('M d, Y')} to {$dateRange[1]->format('M d, Y')}, expenses were recorded across " . count($branchData) . " branches.</p>
        <p>The branch with the highest expenses was <strong>{$topBranch}</strong>, accounting for {$topBranchPercentage}% of total expenses.</p>
        <p>The average expense per branch was ₦" . number_format($totalExpenses / count($branchData), 2) . ".</p>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Investigate the reasons for higher expenses in the {$topBranch} branch and identify any unusual spending patterns.</li>
            <li>Compare best practices from branches with lower expenses to those with higher expenses.</li>
            <li>Consider implementing a standardized expense policy across all branches to ensure consistency.</li>
            <li>Set branch-specific expense targets based on factors such as size, location, and operational needs.</li>
        </ul>";
    
        return [
            'title' => 'Expenses by Branch Report',
            'chartData' => $pieChartData,
            'barChartData' => $barChartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($branchData, ['Branch', 'Amount']),
        ];
    }

    private function generateTopExpensesReport($expenses, $dateRange, $limit = 5)
    {
        $topExpenses = $expenses->sortByDesc('amount')->take($limit);
    
        $topExpensesData = $topExpenses->pluck('amount', 'note');
    
        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $topExpensesData->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $topExpensesData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($topExpensesData)),
                    ],
                ],
            ],
            'options' => [
                'indexAxis' => 'y',
            ],
            'title' => "Top {$limit} Expenses",
        ];
    
        $totalTopExpenses = $topExpensesData->sum();
        $totalAllExpenses = $expenses->sum('amount');
        $topExpensesPercentage = $totalAllExpenses > 0 ? number_format(($totalTopExpenses / $totalAllExpenses) * 100, 1) : 0;
    
        $analysis = "
        <h4>Top Expenses Analysis</h4>
        <p>From {$dateRange[0]->format('M d, Y')} to {$dateRange[1]->format('M d, Y')}, the top {$limit} expenses accounted for {$topExpensesPercentage}% of total expenses.</p>
        <p>The highest single expense was ₦" . number_format($topExpensesData->first(), 2) . " for '{$topExpensesData->keys()->first()}'.</p>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Review each of these top expenses to ensure they are necessary and cost-effective.</li>
            <li>For recurring expenses in this list, explore long-term contracts or bulk purchases to potentially reduce costs.</li>
            <li>Implement a review process for expenses exceeding ₦" . number_format($topExpensesData->last(), 2) . " to better control high-value transactions.</li>
            <li>Consider negotiating with vendors or exploring alternative suppliers for these high-cost items or services.</li>
        </ul>";
    
        return [
            'title' => "Top {$limit} Expenses Report",
            'chartData' => $chartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($topExpensesData, ['Expense Description', 'Amount']),
        ];
    }

    private function generateTrendAnalysisReport($expenses, $dateRange)
    {
        $expensesGroupedByDate = $expenses->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        })->map->sum('amount');
    
        // Calculate 7-day moving average
        $movingAverage = [];
        foreach ($expensesGroupedByDate as $date => $amount) {
            $startDate = Carbon::parse($date)->subDays(6);
            $endDate = Carbon::parse($date);
            $averageAmount = $expenses->whereBetween('created_at', [$startDate, $endDate])->avg('amount');
            $movingAverage[$date] = $averageAmount;
        }
    
        $chartData = [
            'type' => 'line',
            'data' => [
                'labels' => $expensesGroupedByDate->keys()->toArray(),
                'datasets' => [
                    [
                        'label' => 'Daily Expenses',
                        'data' => $expensesGroupedByDate->values()->toArray(),
                        'borderColor' => '#36A2EB',
                        'fill' => false,
                    ],
                    [
                        'label' => '7-day Moving Average',
                        'data' => array_values($movingAverage),
                        'borderColor' => '#FF6384',
                        'fill' => false,
                    ],
                ],
            ],
            'title' => 'Expense Trend Analysis',
        ];
    
        $totalExpenses = $expensesGroupedByDate->sum();
        $avgDailyExpense = $expensesGroupedByDate->avg();
        $maxExpenseDay = $expensesGroupedByDate->keys()->last();
        $maxExpenseAmount = $expensesGroupedByDate->max();
    
        $analysis = "
        <h4>Expense Trend Analysis</h4>
        <p>From {$dateRange[0]->format('M d, Y')} to {$dateRange[1]->format('M d, Y')}, the total expenses amounted to ₦" . number_format($totalExpenses, 2) . ".</p>
        <p>The average daily expense was ₦" . number_format($avgDailyExpense, 2) . ".</p>
        <p>The highest daily expense of ₦" . number_format($maxExpenseAmount, 2) . " occurred on " . Carbon::parse($maxExpenseDay)->format('M d, Y') . ".</p>
        
        <h4>Key Observations</h4>
        <ul>
            <li>The 7-day moving average helps smooth out daily fluctuations and shows the overall trend more clearly.</li>
            <li>There are " . $expensesGroupedByDate->count() . " days with recorded expenses in this period.</li>
            <li>" . ($expensesGroupedByDate->count() > 1 ? "The trend line " . ($expensesGroupedByDate->first() < $expensesGroupedByDate->last() ? "shows an overall increase" : "shows an overall decrease") . " in daily expenses over the period." : "There's not enough data to determine a clear trend.") . "</li>
        </ul>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Investigate the causes of high-expense days, particularly " . Carbon::parse($maxExpenseDay)->format('M d, Y') . ", to understand and potentially mitigate similar spikes in the future.</li>
            <li>Use the 7-day moving average as a benchmark for setting daily expense targets.</li>
            <li>Consider implementing cost-saving measures during periods when the trend shows consistent increases.</li>
            <li>Regularly review this trend analysis to identify seasonal patterns or unexpected changes in expense behavior.</li>
        </ul>";
    
        return [
            'title' => 'Expense Trend Analysis Report',
            'chartData' => $chartData,
            'analysis' => $analysis,
            'table' => null,
        ];
    }

    private function generateMonthlySummaryReport($expenses, $dateRange)
    {
        $monthlyData = $expenses->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        })->map->sum('amount');
    
        // Get data for the same months in the previous year, if available
        $previousYearData = $expenses->filter(function($expense) use ($dateRange) {
            return $expense->created_at < $dateRange[0] && $expense->created_at >= $dateRange[0]->copy()->subYear();
        })->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m');
        })->map->sum('amount');
    
        $labels = $monthlyData->keys()->map(function($date) {
            return Carbon::parse($date)->format('M Y');
        })->toArray();
    
        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Monthly Expenses',
                        'data' => $monthlyData->values()->toArray(),
                        'backgroundColor' => '#36A2EB',
                    ],
                ],
            ],
            'title' => 'Monthly Expense Summary',
        ];
    
        if ($previousYearData->isNotEmpty()) {
            $chartData['data']['datasets'][] = [
                'label' => 'Previous Year',
                'data' => $previousYearData->values()->toArray(),
                'backgroundColor' => '#FF6384',
            ];
        }
    
        $totalExpenses = $monthlyData->sum();
        $avgMonthlyExpense = $monthlyData->avg();
        $maxExpenseMonth = $monthlyData->keys()->last();
        $maxExpenseAmount = $monthlyData->max();
    
        $analysis = "
        <h4>Monthly Expense Summary Analysis</h4>
        <p>From {$dateRange[0]->format('M Y')} to {$dateRange[1]->format('M Y')}, the total expenses amounted to ₦" . number_format($totalExpenses, 2) . ".</p>
        <p>The average monthly expense was ₦" . number_format($avgMonthlyExpense, 2) . ".</p>
        <p>The highest monthly expense of ₦" . number_format($maxExpenseAmount, 2) . " occurred in " . Carbon::parse($maxExpenseMonth)->format('M Y') . ".</p>
        
        <h4>Key Observations</h4>
        <ul>
            <li>There are " . $monthlyData->count() . " months with recorded expenses in this period.</li>
            <li>" . ($monthlyData->count() > 1 ? "The monthly expenses " . ($monthlyData->first() < $monthlyData->last() ? "show an overall increase" : "show an overall decrease") . " over the period." : "There's not enough data to determine a clear trend.") . "</li>
            " . ($previousYearData->isNotEmpty() ? "<li>Compared to the previous year, " . ($monthlyData->sum() > $previousYearData->sum() ? "expenses have increased" : "expenses have decreased") . ".</li>" : "") . "
        </ul>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Investigate the causes of high-expense months, particularly " . Carbon::parse($maxExpenseMonth)->format('M Y') . ", to understand and potentially mitigate similar spikes in the future.</li>
            <li>Use the average monthly expense of ₦" . number_format($avgMonthlyExpense, 2) . " as a benchmark for setting monthly budgets.</li>
            <li>Consider implementing cost-saving measures during historically high-expense months.</li>
            <li>Regularly review this monthly summary to identify seasonal patterns or unexpected changes in expense behavior.</li>
        </ul>";
    
        return [
            'title' => 'Monthly Expense Summary Report',
            'chartData' => $chartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($monthlyData, ['Month', 'Amount']),
        ];
    }

    private function generateYearlySummaryReport($expenses, $dateRange)
    {
        $yearlyData = $expenses->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->map->sum('amount');
    
        $labels = $yearlyData->keys()->toArray();
    
        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Yearly Expenses',
                        'data' => $yearlyData->values()->toArray(),
                        'backgroundColor' => $this->getColorPalette(count($yearlyData)),
                    ],
                ],
            ],
            'title' => 'Yearly Expense Summary',
        ];
    
        $totalExpenses = $yearlyData->sum();
        $avgYearlyExpense = $yearlyData->avg();
        $maxExpenseYear = $yearlyData->keys()->last();
        $maxExpenseAmount = $yearlyData->max();
    
        $yearOverYearChange = [];
        for ($i = 1; $i < count($labels); $i++) {
            $previousYear = $yearlyData[$labels[$i-1]];
            $currentYear = $yearlyData[$labels[$i]];
            $change = ($currentYear - $previousYear) / $previousYear * 100;
            $yearOverYearChange[$labels[$i]] = number_format($change, 1);
        }
    
        $analysis = "
        <h4>Yearly Expense Summary Analysis</h4>
        <p>From {$labels[0]} to {$labels[count($labels)-1]}, the total expenses amounted to ₦" . number_format($totalExpenses, 2) . ".</p>
        <p>The average yearly expense was ₦" . number_format($avgYearlyExpense, 2) . ".</p>
        <p>The highest yearly expense of ₦" . number_format($maxExpenseAmount, 2) . " occurred in {$maxExpenseYear}.</p>
        
        <h4>Key Observations</h4>
        <ul>
            <li>There are " . count($labels) . " years with recorded expenses in this period.</li>
            <li>" . (count($labels) > 1 ? "The yearly expenses " . ($yearlyData->first() < $yearlyData->last() ? "show an overall increase" : "show an overall decrease") . " over the period." : "There's not enough data to determine a clear trend.") . "</li>
            " . (count($yearOverYearChange) > 0 ? "<li>Year-over-year changes:<ul>" . implode('', array_map(function($year, $change) {
                return "<li>{$year}: " . ($change >= 0 ? "+" : "") . "{$change}%</li>";
            }, array_keys($yearOverYearChange), $yearOverYearChange)) . "</ul></li>" : "") . "
        </ul>
    
        <h4>Recommendations</h4>
        <ul>
            <li>Investigate the causes of high-expense years, particularly {$maxExpenseYear}, to understand and potentially mitigate similar increases in the future.</li>
            <li>Use the average yearly expense of ₦" . number_format($avgYearlyExpense, 2) . " as a benchmark for setting annual budgets.</li>
            <li>Analyze the factors contributing to significant year-over-year changes, both positive and negative.</li>
            <li>Develop long-term strategies to manage and reduce expenses based on the observed trends.</li>
            <li>Consider setting multi-year expense reduction targets to improve overall financial performance.</li>
        </ul>";
    
        return [
            'title' => 'Yearly Expense Summary Report',
            'chartData' => $chartData,
            'analysis' => $analysis,
            'table' => $this->generateTableHtml($yearlyData, ['Year', 'Amount']),
        ];
    }

    private function getColorPalette($count)
    {
        $baseColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }
        return $colors;
    }

    private function generateTableHtml($data, $headers)
    {
        $html = '<table class="table table-striped"><thead><tr>';
        foreach ($headers as $header) {
            $html .= "<th>$header</th>";
        }
        $html .= '</tr></thead><tbody>';
        foreach ($data as $key => $value) {
            $html .= "<tr><td>$key</td><td>₦" . number_format($value, 2) . "</td></tr>";
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
