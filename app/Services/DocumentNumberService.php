<?php

namespace App\Services;

use App\Models\CodeMaster;

class DocumentNumberService
{
    public static function generate(string $docType): string
    {
        $period = now()->format('ym');

        $sequence = CodeMaster::where('doc_type', $docType)
            ->where('period', $period)
            ->lockForUpdate()
            ->first();

        if (!$sequence) {

            $sequence = CodeMaster::create([
                'doc_type'    => $docType,
                'period'      => $period,
                'last_number' => 0,
            ]);
        }

        $sequence->increment('last_number');

        $sequence->refresh();

        $runningNumber = str_pad(
            $sequence->last_number,
            5,
            '0',
            STR_PAD_LEFT
        );

        return $docType . $period . $runningNumber;

    }
}