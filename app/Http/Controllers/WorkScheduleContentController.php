<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramOfWork;
use Illuminate\Support\Facades\DB;

class WorkScheduleContentController extends Controller
{
    public function contents($id)
    {
        $pow_contents = ProgramOfWork::where('program_of_works.id', $id)
            ->with(
                [
                    'b3Project' => function ($q) {
                        $q->select(
                            'id',
                            'registry_no',
                            'project_title',
                            'project_nature_id',
                            'project_nature_type_id',
                            'location',
                            'status'
                        );
                        $q->with(['projectNature' => function ($q) {
                            $q->select('id', 'name');
                        }]);

                        $q->with(['projectNatureType' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                    },
                    'powTable' => function ($q) {
                        $q->select(
                            'pow_tables.id',
                            'pow_tables.program_of_work_id',
                            'pow_tables.sow_category_id')

                            ->join('pow_table_contents', 'pow_table_contents.pow_table_id', 'pow_tables.id')
                            ->join('pow_table_content_dupas', 'pow_table_content_dupas.pow_table_content_id', 'pow_table_contents.id')
                            ->join('dupas', 'dupas.id', '=', 'pow_table_content_dupas.dupa_id')
                            ->join('sow_references', 'sow_references.sow_sub_category_id', 'pow_table_contents.sow_subcategory_id')
                            ->groupBy([
                                'pow_tables.id',
                                'pow_tables.program_of_work_id',
                                'pow_tables.sow_category_id',
                            ]);
                    },
                    'powTable.contents' => function ($q) {
                        $q->select(
                            'pow_table_contents.id',
                            'pow_table_contents.pow_table_id',
                            'pow_table_contents.sow_category_id',
                            'pow_table_contents.sow_subcategory_id',
                            'sow_categories.name as sowcat_name',
                            'sow_sub_categories.item_code as subcat_item_code',
                            'sow_sub_categories.name',
                             )
                            ->join(
                                'sow_sub_categories',
                                'pow_table_contents.sow_subcategory_id',
                                'sow_sub_categories.id',
                            )->join('pow_table_content_dupas', 'pow_table_content_dupas.pow_table_content_id', 'pow_table_contents.id')
                            ->join('dupas', 'dupas.id', '=', 'pow_table_content_dupas.dupa_id')
                            ->join('sow_categories', 'pow_table_contents.sow_category_id', 'sow_categories.id')
                            ->groupBy([
                                'pow_table_contents.id',
                                'pow_table_contents.pow_table_id',
                                'pow_table_contents.sow_category_id',
                                'pow_table_contents.sow_subcategory_id',
                                'sow_sub_categories.item_code',
                                'sow_sub_categories.name',
                                'sow_categories.name',
                            ]);
                    },

                    'powTable.contents.dupaItems' => function ($q) {
                        $q->select(
                            'pow_table_content_dupas.id',
                            'pow_table_content_dupas.pow_table_content_id',
                            'pow_table_content_dupas.dupa_id',
                            'pow_table_content_dupas.quantity',
                            'dupas.item_number',
                            'dupas.description as description',
                            'dupas.output_per_hour',
                            'unit_of_measurements.abbreviation as unit',
                            DB::raw('ROUND((dupas.output_per_hour * pow_table_content_dupas.quantity)/8,0) as duration'),
                            DB::raw('ROUND((dupas.output_per_hour * pow_table_content_dupas.quantity)/8,0) as duration'),

                            )
                            ->join('dupas', 'dupas.id', 'pow_table_content_dupas.dupa_id')
                            ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupas.unit_id');
                    }
                ],
            )
            ->join('pow_tables', 'pow_tables.program_of_work_id', 'program_of_works.id')
            ->join('pow_table_contents', 'pow_table_contents.pow_table_id', 'pow_tables.id')
            ->join('pow_table_content_dupas', 'pow_table_content_dupas.pow_table_content_id', 'pow_table_contents.id')
            ->join('dupas', 'dupas.id', '=', 'pow_table_content_dupas.dupa_id')
            ->select(
                'program_of_works.id',
                'program_of_works.b3_project_id',
                'pow_tables.program_of_work_id',

               )
            ->groupBy([
                'program_of_works.id',
                'program_of_works.b3_project_id',
                'pow_tables.program_of_work_id',
            ])
            ->get();

            foreach ($pow_contents as $pow) {
                foreach ($pow->powTable as $table) {
                    foreach ($table->contents as $content) {
                        foreach ($content->dupaItems as $item) {
                            $description = $item->description;
                            $duration = $item->duration;

                            info($description);
                            info($duration);

                        }
                    }
                }
            }

        return response()->json($pow_contents);
    }
}
