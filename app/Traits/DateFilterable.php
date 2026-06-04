<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait DateFilterable
{
    /**
     * Apply a date-range filter to a query based on the `date_filter` request parameter.
     *
     * Supported values:
     *   today, yesterday, last_7_days, last_30_days,
     *   this_month, last_month, this_year, last_year, all
     *
     * @param  Builder  $query   The Eloquent query builder to filter
     * @param  string|null  $filter  The date filter value from the request
     * @param  string  $column  The column name to filter on (default: created_at)
     * @return Builder
     */
    protected function applyDateFilter(Builder $query, ?string $filter, string $column = 'created_at'): Builder
    {
        if (!$filter || $filter === 'all') {
            return $query;
        }

        $now = Carbon::now();

        switch ($filter) {
            case 'today':
                $query->whereDate($column, $now->toDateString());
                break;

            case 'yesterday':
                $query->whereDate($column, $now->copy()->subDay()->toDateString());
                break;

            case 'last_7_days':
                $query->whereDate($column, '>=', $now->copy()->subDays(7)->toDateString());
                break;

            case 'last_30_days':
                $query->whereDate($column, '>=', $now->copy()->subDays(30)->toDateString());
                break;

            case 'this_month':
                $query->whereMonth($column, $now->month)
                      ->whereYear($column, $now->year);
                break;

            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                $query->whereMonth($column, $lastMonth->month)
                      ->whereYear($column, $lastMonth->year);
                break;

            case 'this_year':
                $query->whereYear($column, $now->year);
                break;

            case 'last_year':
                $query->whereYear($column, $now->copy()->subYear()->year);
                break;
        }

        return $query;
    }
}
