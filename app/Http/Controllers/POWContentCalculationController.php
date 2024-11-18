<?php

namespace App\Http\Controllers;

use App\Models\ProgramOfWork;
use Illuminate\Support\Facades\DB;

class POWContentCalculationController extends Controller
{
    public function content($id)
    {
        // return $id;
        $pow_contents = ProgramOfWork::where('program_of_works.b3_project_id', $id)
            ->with(
                [
                    'group',
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
                        $q->with(['dupaPerProjectGroup' => function ($q) {
                            $q->select('id', 'name', 'group_no', 'b3_project_id');
                        }]);
                    },
                    'powTable' => function ($q) {
                        $q->select(
                            'pow_tables.id',
                            'pow_tables.program_of_work_id',
                            'pow_tables.sow_category_id',
                            'sow_categories.name as part_number_sowcat_name_',
                            'sow_categories.item_code as part_number_sowcat_item_code',
                            'per_part_computations_table.grand_total',
                            'per_part_computations_table.ocm',
                            'per_part_computations_table.profit',
                            'per_part_computations_table.percentage',
                            'per_part_computations_table.total_per_part',
                            'per_part_computations_table.total_markup_value_per_part_number',
                            'per_part_computations_table.total_vat_per_part_number',
                            'per_part_computations_table.total_indirect_cost_per_part_number',
                            'per_part_computations_table.total_cost_per_part_number',
                            // DB::raw('
                            //     (
                            //         SELECT name
                            //         FROM sow_references
                            //         JOIN sow_sub_categories
                            //         ON
                            //         sow_references.parent_sow_sub_category_id = sow_sub_categories.id
                            //         WHERE sow_references.sow_sub_category_id = per_part_computations_table.sow_subcategory_id
                            //     ) AS parent'),
                        )
                            ->leftJoin(DB::raw(
                                "
                                (
                                    SELECT
                                        pow_table_contents.pow_table_id,

                                        pow_table_contents.sow_category_id,
                                        dupa_per_projects.direct_unit_cost,
                                        pow_table_content_dupas.quantity,
                                        pow_table_contents.sow_subcategory_id,
                                        per_part_total_table.total_per_part,
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
                                            pow_table_contents.pow_table_id,
                                            sow_categories.item_code,
                                            SUM(pow_table_content_dupas.total_estimated_direct_cost) as total_per_part
                                            FROM pow_tables
                                            JOIN pow_table_contents ON pow_table_contents.pow_table_id = pow_tables.id
                                            JOIN sow_categories ON pow_table_contents.sow_category_id = sow_categories.id
                                            JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                                            GROUP by pow_table_contents.pow_table_id,
                                                     sow_categories.item_code
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
                                        GROUP BY program_of_works.id
                                    ) as grand_total_table ON grand_total_table.id = pow_tables.program_of_work_id
                                ) as per_part_computations_table"
                            ), 'pow_tables.id', 'per_part_computations_table.pow_table_id')
                            ->join('sow_categories', 'sow_categories.id', 'pow_tables.sow_category_id')
                            ->groupBy([
                                'pow_tables.id',
                                'pow_tables.sow_category_id',
                                'pow_tables.program_of_work_id',
                                'sow_categories.item_code',
                                'sow_categories.name',
                                // 'parent',
                            ]);
                    },
                    'powTable.contents' => function ($q) {
                        $q->select(
                            'pow_table_contents.id',
                            'sow_sub_categories.name as part_letter_sub_cat_name',
                            'sow_sub_categories.item_code as part_letter_sub_cat_item_code',
                            'pow_table_contents.pow_table_id',
                            'per_letter_computations_table.grand_total',
                            'per_letter_computations_table.total_per_letter',
                            'per_letter_computations_table.ocm',
                            'per_letter_computations_table.profit',
                            'per_letter_computations_table.percentage',
                            'per_letter_computations_table.markup_value_per_letter',
                            'per_letter_computations_table.vat_per_letter',
                            'per_letter_computations_table.indirect_cost_per_letter',
                            'per_letter_computations_table.cost_per_letter',
                        )
                            ->leftJoin(DB::raw(
                                "
                                (
                                    SELECT
                                        pow_table_contents.pow_table_id,
                                        pow_table_contents.sow_category_id,
                                        pow_table_contents.sow_subcategory_id,
                                        pow_table_content_dupas.pow_table_content_id,
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

                                            pow_table_content_dupas.pow_table_content_id,
                                            ROUND(SUM(pow_table_content_dupas.total_estimated_direct_cost), 2) AS total_per_letter
                                        FROM pow_table_content_dupas
                                        JOIN pow_table_contents ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id

                                        GROUP BY

                                            pow_table_content_dupas.pow_table_content_id
                                    ) as per_letter_total_table ON per_letter_total_table.pow_table_content_id = pow_table_contents.id
                                    JOIN pow_tables ON pow_tables.id = pow_table_contents.pow_table_id
                                    JOIN program_of_works ON program_of_works.id = pow_tables.program_of_work_id
                                    JOIN (
                                        SELECT
                                            program_of_works.id,
                                            ROUND(SUM(pow_table_content_dupas.total_estimated_direct_cost),2) as grand_total
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
                            ->join('sow_sub_categories', 'sow_sub_categories.id', 'pow_table_contents.sow_subcategory_id')
                            ->groupBy([
                                'pow_table_contents.pow_table_id',
                                'pow_table_contents.id',
                                'sow_sub_categories.name',
                                'sow_sub_categories.item_code',
                            ]);
                    },
                    'powTable.contents.dupaItemsPerProject' => function ($q) {
                        $q->select(
                            'pow_table_content_dupas.id',
                            'pow_table_content_dupas.pow_table_content_id',
                            'per_dupa_computations_table.dupa_per_project_id',
                            'per_dupa_computations_table.description',
                            'per_dupa_computations_table.item_number',
                            'per_dupa_computations_table.direct_unit_cost',
                            'per_dupa_computations_table.name as unit_measure_name',
                            'per_dupa_computations_table.abbreviation as unit_measure_abbreviation',
                            'per_dupa_computations_table.markup_value_per_dupa',
                            'per_dupa_computations_table.vat_per_dupa',
                            'per_dupa_computations_table.indirect_cost_per_dupa',
                            'per_dupa_computations_table.cost_per_dupa',
                            'pow_table_content_dupas.total_estimated_direct_cost',
                            'pow_table_content_dupas.quantity',
                            'per_dupa_computations_table.ocm',
                            'per_dupa_computations_table.profit',
                            'per_dupa_computations_table.percentage',
                            'per_dupa_computations_table.table_say',
                        )->join(DB::raw(

                            "
                        (
                            SELECT
                                pow_table_content_dupas.id,
                                pow_table_contents.pow_table_id,
                                pow_table_contents.sow_category_id,
                                pow_table_contents.sow_subcategory_id,
                                pow_table_content_dupas.pow_table_content_id,
                                dupa_per_projects.id as dupa_per_project_id,
                                dupa_per_projects.description,
                                dupa_per_projects.item_number,
                                dupa_per_projects.direct_unit_cost,
                                dupa_per_projects.unit_id,
                                unit_of_measurements.name,
                                unit_of_measurements.abbreviation,
                                pow_table_content_dupas.total_estimated_direct_cost,
                                take_off_tables.table_say,
                                (SELECT ROUND(SUM(pow_table_content_dupas.total_estimated_direct_cost),2) FROM pow_table_content_dupas) as grand_total,
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
                                (SELECT ROUND(pow_table_content_dupas.total_estimated_direct_cost * percentage, 2)) as markup_value_per_dupa,
                                (SELECT ROUND((pow_table_content_dupas.total_estimated_direct_cost + markup_value_per_dupa)  * 0.05, 2)) as vat_per_dupa,
                                (SELECT ROUND(pow_table_content_dupas.total_estimated_direct_cost * percentage + vat_per_dupa, 2)) as indirect_cost_per_dupa,
                                (SELECT ROUND((pow_table_content_dupas.total_estimated_direct_cost + markup_value_per_dupa) + vat_per_dupa, 2)) as cost_per_dupa

                            FROM
                                pow_table_contents
                            JOIN pow_table_content_dupas ON pow_table_content_dupas.pow_table_content_id = pow_table_contents.id
                            JOIN dupa_per_projects ON dupa_per_projects.id = pow_table_content_dupas.dupa_per_project_id
                            JOIN dupas ON dupas.id = dupa_per_projects.dupa_id
                            JOIN table_dupa_components ON table_dupa_components.dupa_id = dupas.id
                            LEFT JOIN table_dupa_component_formulas ON table_dupa_component_formulas.table_dupa_component_id = table_dupa_components.id
                            LEFT JOIN take_off_tables ON take_off_tables.table_dupa_component_formula_id = table_dupa_component_formulas.id
                            JOIN unit_of_measurements ON unit_of_measurements.id = dupa_per_projects.unit_id
                            JOIN sow_references ON sow_references.sow_sub_category_id = pow_table_contents.sow_subcategory_id
                            
                        )
                            as per_dupa_computations_table
                        "
                        ), "pow_table_content_dupas.id", "per_dupa_computations_table.id")
                            ->groupBy([
                                'pow_table_content_dupas.id',
                                'pow_table_content_dupas.pow_table_content_id',
                                'per_dupa_computations_table.dupa_per_project_id',
                                'per_dupa_computations_table.description',
                                'per_dupa_computations_table.item_number',
                                'per_dupa_computations_table.direct_unit_cost',
                                'per_dupa_computations_table.name',
                                'per_dupa_computations_table.abbreviation',
                                'per_dupa_computations_table.markup_value_per_dupa',
                                'per_dupa_computations_table.vat_per_dupa',
                                'per_dupa_computations_table.indirect_cost_per_dupa',
                                'per_dupa_computations_table.cost_per_dupa',
                                'pow_table_content_dupas.total_estimated_direct_cost',
                                'pow_table_content_dupas.quantity',
                                'per_dupa_computations_table.ocm',
                                'per_dupa_computations_table.profit',
                                'per_dupa_computations_table.percentage',
                            ]);
                    }
                ],

            )

            ->select(
                'program_of_works.id',
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
            ), "program_of_works.id", "overall_computation_table.program_of_work_id")

            ->groupBy('program_of_works.id', 'program_of_works.b3_project_id')



            ->get();

        return response()->json($pow_contents);
    }
}
