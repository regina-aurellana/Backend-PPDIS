<?php

namespace App\Http\Services\DupaPerProject\LOME;

use App\Models\DupaMaterialPerProject;
use App\Models\DupaPerProject;
use App\Models\LOME;

class LOMEService
{
    public function LomeFromDupa($b3_project_id)
    {
        $dupa_per_proj_materials_quantity = [];
        $dupa_material_per_project = DupaMaterialPerProject::whereHas('dupaContentPerProject.dupaPerProject', function ($q) use ($b3_project_id) {
            $q->where('b3_project_id', $b3_project_id);
        })->get();

        foreach ($dupa_material_per_project as $item) {
            $item_material_id = $item['material_id'];
            $item_material_quantity = $item['quantity'];
            if (array_key_exists($item_material_id, $dupa_per_proj_materials_quantity)) {
                $dupa_per_proj_materials_quantity[$item_material_id]['quantity'] += $item_material_quantity;
            } else {
                $dupa_per_proj_materials_quantity[$item_material_id] = array('item_id' => $item_material_id, 'quantity' => $item_material_quantity, 'b3_project_id' => $b3_project_id);
            }
        }

        foreach ($dupa_per_proj_materials_quantity as $item) {
            LOME::UpdateOrCreate(
                [
                    'b3_project_id' => $item['b3_project_id'],
                    'material_id' => $item['item_id'],
                ],
                [
                    'quantity' => $item['quantity']
                ]
            );
        }
    }
}
