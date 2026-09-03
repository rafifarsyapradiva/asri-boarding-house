<?php

namespace App\Services;

class CalendarStyleHelper
{
    /**
     * Map event type and status to Tailwind CSS classes.
     */
    public static function getColorClasses(string $type, ?string $status = null, int $lateMonths = 0): string
    {
        switch ($type) {
            case 'survey':
                return 'bg-amber-200 text-amber-900 border-amber-400';
            
            case 'check-in':
                return 'bg-blue-200 text-blue-900 border-blue-400';
            
            case 'check-out':
                return 'bg-purple-200 text-purple-900 border-purple-400';
            
            case 'tagihan':
                if ($status === 'lunas') {
                    return 'bg-slate-100 text-slate-600 border-slate-300 line-through opacity-70';
                }

                if ($status === 'terlambat' || $lateMonths > 0) {
                    if ($lateMonths === 2) {
                        return 'bg-yellow-300 text-yellow-900 border-yellow-500';
                    }
                    if ($lateMonths >= 3) {
                        return 'bg-red-400 text-red-900 border-red-600';
                    }
                    return 'bg-green-300 text-green-900 border-green-500';
                }

                // Default pending / normal tagihan
                return 'bg-slate-200 text-slate-900 border-slate-400';

            default:
                return 'bg-slate-200 text-slate-900 border-slate-400';
        }
    }
}
