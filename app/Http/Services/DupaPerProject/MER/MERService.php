<?php

namespace App\Http\Services\DupaPerProject\MER;

use App\Models\MER;

class MERService
{
    public function MERFromDupa($data, $b3_project_id)
    {
        foreach ($data as $item) {
            MER::firstOrCreate(
                [
                    'b3_project_id' => $b3_project_id,
                    'equipment_id' => $item['equipment_id'],
                ],
                [
                    'quantity' => 1
                ]
            );
        }
    }
}
