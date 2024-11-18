<?php

namespace App\Http\Controllers;

use App\Http\Requests\ABCContent\AddABCContentRequest;
use App\Http\Requests\ABCContent\UpdateABCContentRequest;
use App\Models\ABC;
use App\Models\ABCContent;
use App\Models\PowTableContent;
use App\Models\PowTableContentDupa;
use App\Models\ProgramOfWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ABCContentController extends Controller
{
    public function getContent($id = null)
    {
        try {
            $abcs = ABC::with(
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

                    'b3Project.programOfWork.powTable' => function ($q) {
                        //PART NUMBER
                        $q->select(
                            'pow_tables.id',
                            'pow_tables.program_of_work_id',
                            'pow_tables.sow_category_id',
                            'per_part_computations_table.sow_category_name',
                            'per_part_computations_table.sow_category_item_code',
                            'per_part_computations_table.grand_total',
                            'per_part_computations_table.ocm',
                            'per_part_computations_table.profit',
                            'per_part_computations_table.percentage',
                            'per_part_computations_table.total_per_part',
                            'per_part_computations_table.total_markup_value_per_part_number',
                            'per_part_computations_table.total_vat_per_part_number',
                            'per_part_computations_table.total_indirect_cost_per_part_number',
                            'per_part_computations_table.total_cost_per_part_number',
                            DB::raw('
                                    (
                                        SELECT name
                                        FROM sow_references
                                        JOIN sow_sub_categories
                                        ON
                                        sow_references.parent_sow_sub_category_id = sow_sub_categories.id
                                        WHERE sow_references.sow_sub_category_id = per_part_computations_table.sow_subcategory_id
                                    ) AS parent'),
                        )
                            ->join(DB::raw(
                                "
                                    (
                                        SELECT
                                            pow_table_contents.pow_table_id,
                                            pow_table_contents.sow_category_id,
                                            dupa_per_projects.direct_unit_cost,
                                            pow_table_content_dupas.quantity,
                                            pow_table_contents.sow_subcategory_id,
                                            per_part_total_table.total_per_part,
                                            per_part_total_table.name as sow_category_name,
                                            per_part_total_table.item_code as sow_category_item_code,
                                            grand_total_table.grand_total,
                                            (
                                                SELECT CASE
                                                    WHEN grand_total <= 5000000 THEN 0.15
                                                    WHEN grand_total > 5000000 AND grand_total <= 50000000 THEN 0.12
                                                    WHEN grand_total > 50000000 AND grand_total<= 150000000 THEN 0.10
                                                    ELSE 0.08
                                                END
                                            ) as ocm,
                                            (SELECT CASE WHEN grand_total <= 5000000 THEN 0.10 ELSE 0.08 END) as profit,
                                            (SELECT ocm + profit) as percentage,
                                            (SELECT per_part_total_table.total_per_part * percentage) as total_markup_value_per_part_number,
                                            (SELECT (per_part_total_table.total_per_part + total_markup_value_per_part_number)  * 0.05) as total_vat_per_part_number,
                                            (SELECT per_part_total_table.total_per_part * percentage + total_vat_per_part_number) as total_indirect_cost_per_part_number,
                                            (SELECT (per_part_total_table.total_per_part + total_markup_value_per_part_number) + total_vat_per_part_number) as total_cost_per_part_number

                                        FROM
                                            pow_table_contents
                                        JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                                        JOIN dupa_per_projects ON dupa_per_projects.id = pow_table_content_dupas.dupa_per_project_id
                                        JOIN sow_references ON sow_references.sow_sub_category_id = pow_table_contents.sow_subcategory_id
                                        JOIN (
                                                SELECT
                                                    sow_categories.name,
                                                    sow_categories.item_code,
                                                    pow_table_contents.pow_table_id,
                                                    SUM(pow_table_content_dupas.total_estimated_direct_cost) as total_per_part
                                                FROM pow_tables
                                                JOIN pow_table_contents ON pow_table_contents.pow_table_id = pow_tables.id
                                                JOIN sow_categories ON pow_table_contents.sow_category_id = sow_categories.id
                                                JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                                                GROUP BY
                                                    sow_categories.name,
                                                    sow_categories.item_code,
                                                    pow_table_contents.pow_table_id
                                        ) as per_part_total_table ON per_part_total_table.pow_table_id = pow_table_contents.pow_table_id
                                        JOIN pow_tables ON pow_tables.id = pow_table_contents.pow_table_id
                                        JOIN program_of_works ON program_of_works.id = pow_tables.program_of_work_id
                                        JOIN (
                                            SELECT
                                                program_of_works.id,
                                                SUM(pow_table_content_dupas.total_estimated_direct_cost) as grand_total
                                            FROM program_of_works
                                            JOIN pow_tables ON program_of_works.id = pow_tables.program_of_work_id
                                            JOIN pow_table_contents ON pow_tables.id = pow_table_contents.pow_table_id
                                            JOIN pow_table_content_dupas ON pow_table_contents.id = pow_table_content_dupas.pow_table_content_id
                                            GROUP BY
                                                program_of_works.id
                                        ) as grand_total_table ON grand_total_table.id = pow_tables.program_of_work_id
                                    ) as per_part_computations_table"
                            ), 'pow_tables.id', 'per_part_computations_table.pow_table_id')
                            ->groupBy([
                                'pow_tables.id',
                                'pow_tables.program_of_work_id',
                                'pow_tables.sow_category_id',
                                'parent'
                            ]);
                    },

                    'b3Project.programOfWork.powTable.contents' => function ($q) {
                        //PART LETTER
                        $q->select(
                            'pow_table_contents.id',
                            'pow_table_contents.pow_table_id',
                            'per_letter_computations_table.grand_total',
                            'per_letter_computations_table.total_per_letter',
                            'per_letter_computations_table.sow_sub_caterory_name',
                            'per_letter_computations_table.sow_sub_item_code',
                            'per_letter_computations_table.ocm',
                            'per_letter_computations_table.profit',
                            'per_letter_computations_table.percentage',
                            'per_letter_computations_table.markup_value_per_letter',
                            'per_letter_computations_table.vat_per_letter',
                            'per_letter_computations_table.indirect_cost_per_letter',
                            'per_letter_computations_table.cost_per_letter',
                        )
                            ->join(DB::raw(
                                "
                                    (
                                        SELECT
                                            pow_table_contents.pow_table_id,
                                            pow_table_contents.sow_category_id,
                                            pow_table_contents.sow_subcategory_id,
                                            pow_table_content_dupas.pow_table_content_id,
                                            per_letter_total_table.name as sow_sub_caterory_name,
                                            per_letter_total_table.item_code as sow_sub_item_code,
                                            total_per_letter,
                                            grand_total_table.grand_total,
                                            (
                                                SELECT CASE
                                                    WHEN grand_total <= 5000000 THEN 0.15
                                                    WHEN grand_total > 5000000 AND grand_total <= 50000000 THEN 0.12
                                                    WHEN grand_total > 50000000 AND grand_total <= 150000000 THEN 0.10
                                                    ELSE 0.08
                                                END
                                            ) as ocm,
                                            (SELECT CASE WHEN grand_total <= 5000000 THEN 0.10 ELSE 0.08 END) as profit,
                                            (SELECT ocm + profit) as percentage,
                                            (SELECT total_per_letter * percentage) as markup_value_per_letter,
                                            (SELECT (total_per_letter + markup_value_per_letter)  * 0.05) as vat_per_letter,
                                            (SELECT total_per_letter * percentage + vat_per_letter) as indirect_cost_per_letter,
                                            (SELECT (total_per_letter + markup_value_per_letter) + vat_per_letter) as cost_per_letter
                                        FROM
                                            pow_table_contents
                                        JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                                        JOIN dupa_per_projects ON dupa_per_projects.id = pow_table_content_dupas.dupa_per_project_id
                                        JOIN sow_references ON sow_references.sow_sub_category_id = pow_table_contents.sow_subcategory_id
                                        JOIN (
                                            SELECT
                                                sow_sub_categories.name,
                                                sow_sub_categories.item_code,
                                                pow_table_content_dupas.pow_table_content_id,
                                                SUM(pow_table_content_dupas.total_estimated_direct_cost) AS total_per_letter
                                            FROM pow_table_content_dupas
                                            JOIN pow_table_contents ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                                            JOIN sow_sub_categories ON pow_table_contents.sow_subcategory_id = sow_sub_categories.id
                                            GROUP BY
                                                sow_sub_categories.name,
                                                sow_sub_categories.item_code,
                                                pow_table_content_dupas.pow_table_content_id
                                        ) as per_letter_total_table ON per_letter_total_table.pow_table_content_id = pow_table_contents.id
                                        JOIN pow_tables ON pow_tables.id = pow_table_contents.pow_table_id
                                        JOIN program_of_works ON program_of_works.id = pow_tables.program_of_work_id
                                        JOIN (
                                            SELECT
                                                program_of_works.id,
                                                SUM(pow_table_content_dupas.total_estimated_direct_cost) as grand_total
                                            FROM program_of_works
                                            JOIN pow_tables ON program_of_works.id = pow_tables.program_of_work_id
                                            JOIN pow_table_contents ON pow_tables.id = pow_table_contents.pow_table_id
                                            JOIN pow_table_content_dupas ON pow_table_contents.id = pow_table_content_dupas.pow_table_content_id
                                            GROUP BY program_of_works.id
                                        ) as grand_total_table ON grand_total_table.id = pow_tables.program_of_work_id
                                    )
                                        as per_letter_computations_table
                                    "
                            ), 'pow_table_contents.id', 'per_letter_computations_table.pow_table_content_id')
                            ->groupBy([
                                'pow_table_contents.pow_table_id',
                                'pow_table_contents.id',
                            ]);
                    }

                ]
                )

            ->join('b3_projects', 'b3_projects.id', 'a_b_c_s.b3_project_id')
                ->join('program_of_works', 'program_of_works.b3_project_id', 'b3_projects.id')
                ->join('pow_tables', 'pow_tables.program_of_work_id', 'program_of_works.id')
                ->join('pow_table_contents', 'pow_table_contents.pow_table_id', 'pow_tables.id')
                ->join('pow_table_content_dupas', 'pow_table_content_dupas.pow_table_content_id', 'pow_table_contents.id')
                ->join('dupa_per_projects', 'dupa_per_projects.id', '=', 'pow_table_content_dupas.dupa_per_project_id')
                ->join('a_b_c_contents', 'a_b_c_contents.abc_id', 'a_b_c_s.id');

            if ($id != null)
            $abcs->where('a_b_c_s.b3_project_id', $id);


            $abcs = $abcs->select(
                'a_b_c_s.b3_project_id as b3',
                'a_b_c_s.id as abc_id',
                'a_b_c_contents.id as abc_content_id',
                'program_of_works.id as pow_id',
                'program_of_works.b3_project_id',
                'overall_computation_table.grand_total',
                'overall_computation_table.ocm',
                'overall_computation_table.profit',
                'overall_computation_table.percentage',
                'overall_computation_table.overall_markup_value',
                'overall_computation_table.overall_vat',
                'overall_computation_table.overall_indirect_cost',
                'overall_computation_table.overall_cost',
                'overall_computation_table.road_and_drainage_total',
                'overall_computation_table.road_and_drainage_markup_value',
                'overall_computation_table.road_and_drainage_vat',
                'overall_computation_table.road_and_drainage_indirect_cost',
                'overall_computation_table.road_and_drainage_cost',
            )->leftJoin(DB::raw(
                '
                (
                    SELECT
                        pow_tables.program_of_work_id,
                        overall_total_table.grand_total,
                        (
                            SELECT CASE
                                WHEN grand_total <= 5000000 THEN 0.15
                                WHEN grand_total > 5000000 AND grand_total <= 50000000 THEN 0.12
                                WHEN grand_total > 50000000 AND grand_total <= 150000000 THEN 0.10
                                ELSE 0.08
                            END
                        ) as ocm,
                        (SELECT CASE WHEN grand_total <= 5000000 THEN 0.10 ELSE 0.08 END) as profit,
                        (SELECT ocm + profit) as percentage,
                        (SELECT grand_total * percentage) as overall_markup_value,
                        (SELECT (grand_total + overall_markup_value)  * 0.05) as overall_vat,
                        (SELECT grand_total * percentage + overall_vat) as overall_indirect_cost,
                        (SELECT (grand_total + overall_markup_value) + overall_vat) as overall_cost,
                        road_and_drainage_table.road_and_drainage_total,
                        (SELECT road_and_drainage_total * percentage) as road_and_drainage_markup_value,
                        (SELECT (road_and_drainage_total + road_and_drainage_markup_value)  * 0.05) as road_and_drainage_vat,
                        (SELECT road_and_drainage_total * percentage + road_and_drainage_vat) as road_and_drainage_indirect_cost,
                        (SELECT (road_and_drainage_total + road_and_drainage_markup_value) + road_and_drainage_vat) as road_and_drainage_cost
                    FROM
                        pow_tables
                    JOIN pow_table_contents ON pow_table_contents.pow_table_id = pow_tables.id
                    JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                    JOIN (
                        SELECT
                            program_of_works.id,
                            SUM(pow_table_content_dupas.total_estimated_direct_cost) as grand_total
                        FROM program_of_works
                        JOIN pow_tables ON program_of_works.id = pow_tables.program_of_work_id
                        JOIN pow_table_contents ON pow_tables.id = pow_table_contents.pow_table_id
                        JOIN pow_table_content_dupas ON pow_table_contents.id = pow_table_content_dupas.pow_table_content_id
                        GROUP BY program_of_works.id
                    ) as overall_total_table ON overall_total_table.id = pow_tables.program_of_work_id
                    JOIN (
                        SELECT
                            program_of_works.id,
                            SUM(CASE WHEN pow_tables.sow_category_id IN (2,3) THEN pow_table_content_dupas.total_estimated_direct_cost ELSE 0 END) as road_and_drainage_total
                        FROM program_of_works
                        JOIN pow_tables ON program_of_works.id = pow_tables.program_of_work_id
                        JOIN pow_table_contents ON pow_tables.id = pow_table_contents.pow_table_id
                        JOIN pow_table_content_dupas ON pow_table_contents.id = pow_table_content_dupas.pow_table_content_id
                        GROUP BY program_of_works.id
                    ) as road_and_drainage_table ON road_and_drainage_table.id = pow_tables.program_of_work_id
                )
                    AS overall_computation_table'
            ), 'program_of_works.id', 'overall_computation_table.program_of_work_id')
            ->groupBy([
                'a_b_c_s.b3_project_id',
                'a_b_c_s.id',
                'a_b_c_contents.id',
                'program_of_works.id',
                'program_of_works.b3_project_id',
            ])

                ->get();



            // Only Show Unique data
            $associativeArray = [];
                foreach ($abcs as $item) {
                    $associativeArray[$item['abc_id']] = $item;
                }
                // Convert the associative array back to indexed array
                $uniqueData = array_values($associativeArray);

                // Now $uniqueData contains only unique items based on the "id" field

                // Convert the unique data back to JSON if needed
                $uniqueData_abc = $uniqueData;


          foreach ($uniqueData_abc as $key => $abc) {
            $decodedData = $abc;

            $b3 = $abc->b3Project->programOfWork[$key];


            // Modify the structure to include only the first item in "program_of_work"
                $modifiedData = [
                    'abc_id' => $decodedData['abc_id'],
                    'abc_content_id' => $decodedData['abc_content_id'],
                    'pow_id' => $b3->id,
                    'b3_project_id' => $decodedData['b3_project_id'],
                    'grand_total' => $decodedData['grand_total'],
                    'ocm' => $decodedData['ocm'],
                    'profit' => $decodedData['profit'],
                    'percentage' => $decodedData['percentage'],
                    'overall_markup_value' => $decodedData['overall_markup_value'],
                        'overall_vat' => $decodedData['overall_vat'],
                        'overall_indirect_cost' => $decodedData['overall_indirect_cost'],
                        'overall_cost' => $decodedData['overall_cost'],
                        'road_and_drainage_total' => $decodedData['road_and_drainage_total'],
                    'road_and_drainage_markup_value' => $decodedData['road_and_drainage_markup_value'],
                    'road_and_drainage_vat' => $decodedData['road_and_drainage_vat'],
                    'road_and_drainage_indirect_cost' => $decodedData['road_and_drainage_indirect_cost'],
                    'road_and_drainage_cost' => $decodedData['road_and_drainage_cost'],
                    'b3_project' => $b3,
                ];

                    $modifiedDataJson[] = $modifiedData;


          }

          foreach ($abcs as $abc) {
            if ($abc->grand_total) {
                $ey = ABCContent::where('abc_id', $abc->abc_id)
                    ->update(['total_cost' => $abc->grand_total]);
            }
        }

          return $modifiedDataJson;




        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function index()
    {
        try {
            $content = $this->getContent();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function create()
    {
        //
    }

    public function store(UpdateABCContentRequest $request)
    {
        try {
            if ($request['id'] != null) {
                ABCContent::where('id', $request['id'])
                    ->update(['abc_id' => $request['abc_id']]);

                return response()->json([
                    'status' => "Updated",
                    'message' => "ABC Content Successfully Updated"
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(int $id)
    {
        try {
            $content = $this->getContent($id);

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(ABCContent $abc_content)
    {
        //
    }

    public function update(Request $request, ABCContent $abc_content)
    {
        //
    }

    public function destroy(ABCContent $abc_content)
    {
        try {
            $abc_content->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'ABC Successfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
