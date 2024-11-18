<?php

use App\Models\District;
use Illuminate\Http\Request;
use App\Models\WorkScheduleItem;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ABCController;
use App\Http\Controllers\MERController;
use App\Http\Controllers\DupaController;
use App\Http\Controllers\LOMEController;
use App\Http\Controllers\LOPEController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaborController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\TakeOffController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PowTableController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DupaLaborController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ABCContentController;
use App\Http\Controllers\B3ProjectsController;
use App\Http\Controllers\DupaContentController;
use App\Http\Controllers\SowCategoryController;
use App\Http\Controllers\CategoryDupaController;
use App\Http\Controllers\DupaMaterialController;
use App\Http\Controllers\TakeOffTableController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\DupaEquipmentController;
use App\Http\Controllers\ProgramOfWorkController;
use App\Http\Controllers\ProjectNatureController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DupaPerProjectController;
use App\Http\Controllers\SowSubCategoryController;

use App\Http\Controllers\POWTableContentController;

use App\Http\Controllers\ProjectDurationController;
use App\Http\Controllers\SubCatReferenceController;
use App\Http\Controllers\WorkScheduleItemController;
use App\Http\Controllers\ProjectNatureTypeController;
use App\Http\Controllers\TakeOffTableFieldController;
use App\Http\Controllers\UnitOfMeasurementController;
use App\Http\Controllers\MinorAndConsumableController;
use App\Http\Controllers\TableDupaComponentController;
use App\Http\Controllers\DupaLaborPerProjectController;
use App\Http\Controllers\DupaPerProjectGroupController;

use App\Http\Controllers\TakeOffTableFormulaController;
use App\Http\Controllers\WorkScheduleContentController;
use App\Http\Controllers\SiteInspectionReportController;
use App\Http\Controllers\CommunicationCategoryController;
use App\Http\Controllers\DupaContentPerProjectController;
use App\Http\Controllers\POWContentCalculationController;
use App\Http\Controllers\DupaMaterialPerProjectController;

use App\Http\Controllers\TakeOffTableFieldInputController;
use App\Http\Controllers\B1ProjectIdentificationController;
use App\Http\Controllers\B3ProjectPlanController;
use App\Http\Controllers\B3Project\SendForApprovalController;
use App\Http\Controllers\DupaEquipmentPerProjectController;
use App\Http\Controllers\TableDupaComponentFormulaController;
use App\Http\Controllers\Communication\CommunicationController;
use App\Http\Controllers\ProjectPlanController;
use App\Models\ProjectPlan;

//-----AUTHENTICATION-----//

// Route::middleware(['auth:sanctum'])->group(function () {

Route::get('get-user', function (Request $request) {
    return $request->user();
});

Route::resource('dupa', DupaController::class);
Route::get('export-dupa', [DupaController::class, 'exportDupa']);

Route::delete('dupa-mat-area/{dupaID}', [DupaContentController::class, 'deleteMaterialArea']);
Route::post('dupa-mat-area/{dupaID}', [DupaContentController::class, 'inputMaterialArea']);
Route::delete('dupa-area/{dupaID}', [DupaContentController::class, 'deleteEquipmentArea']);
Route::post('dupa-area/{dupaID}', [DupaContentController::class, 'inputEquipmentArea']);
Route::post('minor-tool-percentage', [DupaContentController::class, 'minorToolsPercentage']);
Route::resource('content', DupaContentController::class);

Route::get('project/work-schedule/{project}', [B3ProjectsController::class, 'workSchedule']);
Route::resource('project', B3ProjectsController::class);

Route::resource('nature', ProjectNatureController::class);
Route::resource('type', ProjectNatureTypeController::class);
Route::get('type-list/{type}', [ProjectNatureTypeController::class, 'typeList']);

Route::resource('dupalabor', DupaLaborController::class);

Route::resource('dupaequipment', DupaEquipmentController::class);

Route::resource('dupamaterial', DupaMaterialController::class);

Route::get('subcat-list/{subcat}', [SowSubCategoryController::class, 'sowcatList']);
Route::get('subcat-list-descendants/{subcat}', [SowSubCategoryController::class, 'sowcatDescendants']);
Route::get('subcat-list-ancestor/{subcat}', [SowSubCategoryController::class, 'sowcatAncestor']);
Route::get('subcat-first-lvl/{subcat}', [SowSubCategoryController::class, 'firstLevelSub']);
Route::get('subcat-second-lvl/{subcat}', [SowSubCategoryController::class, 'secondLevelSub']);
Route::get('subcat-last-child/{subcat}', [SowSubCategoryController::class, 'getLastChild']);
Route::get('subcat-base-parent/{subcat}', [SowSubCategoryController::class, 'getBaseParent']);
Route::get('subcat-dupa/{subcat}', [SowSubCategoryController::class, 'getDupaOfSubcategory']);
Route::get('sowcat-dupa/{subcat}', [SowSubCategoryController::class, 'getDupaOfSowcategory']);
Route::get('dupa-list-sowcat/{sowcatID}/subcat/{subcat}', [SowSubCategoryController::class, 'getDupaOfSowcatIDsubcatID']);
Route::resource('subcat', SowSubCategoryController::class);

Route::resource('sowcat', SowCategoryController::class);

Route::resource('reference', SubCatReferenceController::class);

Route::resource('measurement', UnitOfMeasurementController::class);

Route::resource('category-dupa', CategoryDupaController::class);

Route::get('take-off-table-input-value/{take_off}', [TakeOffController::class, 'inputValues']);
Route::resource('take-off', TakeOffController::class);

Route::resource('formula', FormulaController::class);

Route::get('dupa-per-project-list/{b3_project_id}', [TakeOffTableController::class, 'dupaPerProjectList']);
Route::post('take-off-table-select', [TakeOffTableController::class, 'selectDupaTakeOff']);
Route::get('take-off-table-by-takeoff-id/{take_off_table}', [TakeOffTableController::class, 'takeOffTablebyTakeoffID']);
Route::post('take-off-table-contingency/{take_off_table}', [TakeOffTableController::class, 'contingency']);
Route::post('take-off-table-transfer-quantity-to-pow/{take_off_table}', [TakeOffTableController::class, 'transferTakeOffDupaQuantityToPOW']);
Route::post('take-off-table-say-total', [TakeOffTableController::class, 'saveSayTotal']);
Route::get('fields', [TakeOffTableController::class, 'field']);
Route::get('take-off-table-list/{take_off_table}', [TakeOffTableController::class, 'getAllTakeOffTables']);
Route::resource('take-off-table', TakeOffTableController::class);

Route::resource('take-off-table-field', TakeOffTableFieldController::class);

Route::get('take-off-table-input-compute/{b3_project_id}', [TakeOffTableFieldInputController::class, 'computePerTable']);
Route::get('take-off-table-field-input-compute/{take_off_table_field_input}', [TakeOffTableFieldInputController::class, 'calculateFormula']);
Route::get('take-off-table-field-input-list/{take_off_table_field_input}', [TakeOffTableFieldInputController::class, 'inputsByTakeOffIdAndTable']);
Route::resource('take-off-table-field-input', TakeOffTableFieldInputController::class);

Route::get('pow-table-list/{program_of_work_id}', [PowTableController::class, 'getPowTablesByPowID']);
Route::get('pow-table-list/{program_of_work_id}/sowcat/{sow_cat_id}', [PowTableController::class, 'powTableID']);
Route::resource('pow-table', PowTableController::class);

// Route::get('take-off-table-formula-compute', [TakeOffTableFormulaController::class, 'computeTable']);
// Route::resource('take-off-table-formula', TakeOffTableFormulaController::class);

Route::post('upload', [ImportController::class, 'upload']);
Route::delete('revert', [ImportController::class, 'revert']);
Route::post('import-subcat', [ImportController::class, 'importSubcatFirstLvl']);
Route::post('import-dupa', [ImportController::class, 'importDupa']);
Route::post('import-takeoff', [ImportController::class, 'takeOffImport']);

//PROGRAM OF WORKS
Route::apiResource('pow', ProgramOfWorkController::class);

Route::post('upload-material', [MaterialController::class, 'uploadMaterial']);
Route::delete('revert-material', [MaterialController::class, 'revertMaterial']);
Route::post('import-material', [MaterialController::class, 'import']);
Route::get('export-material', [MaterialController::class, 'export']);
Route::resource('material', MaterialController::class);

Route::post('upload-labor', [LaborController::class, 'uploadLabor']);
Route::delete('revert-labor', [LaborController::class, 'revertLabor']);
Route::post('import-labor', [LaborController::class, 'importLabor']);
Route::get('export-labor', [LaborController::class, 'exportLabor']);
Route::resource('labor', LaborController::class);

Route::post('upload-equipment', [EquipmentController::class, 'uploadEquipment']);
Route::delete('revert-equipment', [EquipmentController::class, 'revertEquipment']);
Route::post('import-equipment', [EquipmentController::class, 'importEquipment']);
Route::get('export-equipment', [EquipmentController::class, 'exportEquipment']);
Route::resource('equipment', EquipmentController::class);

Route::delete('pow-table-content-dupa-delete/{content}', [POWTableContentController::class, 'destroyContentDupa']);
Route::get('pow-table-content/{program_of_work_id}/part/{part_id}', [POWTableContentController::class, 'contentPart']);
Route::resource('pow-table-content', POWTableContentController::class);
Route::post('pow-table-content-dupa-update/{pow_table_content_dupa}', [POWTableContentController::class, 'updateQuantity']);

//POW CONTENT CALCULATION
Route::get('pow/calculation/content/{id}', [POWContentCalculationController::class, 'contentPowHorizontal']);
Route::get('pow/calculation/content-vertical/{id}', [POWContentCalculationController::class, 'contentPowVertical']);
Route::get('pow/calculation/content/{id}', [POWContentCalculationController::class, 'content']);
Route::get('pow/calculation/total-indirect-cost/{id}', [POWContentCalculationController::class, 'totalIndirectCost']);

Route::resource('abc', ABCController::class);
Route::resource('abc-content', ABCContentController::class);

Route::post('minor-tool/{dupaID}', [MinorAndConsumableController::class, 'addMinorTool']);
Route::delete('minor-tool/{dupaID}', [MinorAndConsumableController::class, 'deleteMinorTool']);

Route::post('consumable/{dupaID}', [MinorAndConsumableController::class, 'addConsumable']);
Route::delete('consumable/{dupaID}', [MinorAndConsumableController::class, 'deleteConsumable']);

Route::resource('work-schedule', WorkScheduleController::class);

Route::resource('work-schedule-item', WorkScheduleItemController::class);
Route::post('work-schedule-item/add-duration/{work_schedule_item}', [WorkScheduleItemController::class, 'storeDuration']);

Route::resource('project-duration', ProjectDurationController::class);

Route::resource('schedule', ScheduleController::class);

Route::get('work-schedule-content', [WorkScheduleContentController::class, 'contents']);

Route::resource('table-dupa-component', TableDupaComponentController::class);

Route::resource('table-dupa-component-formula', TableDupaComponentFormulaController::class);

Route::resource('role', RoleController::class);

Route::resource('team', TeamController::class);

Route::resource('user', UserController::class);

//DUPA PER PROJECT
Route::get('dupa-list-by-project/{b3_project_id}', [DupaPerProjectController::class, 'dupaByProjectID']);
Route::get('dupa-per-project_list/{b3_project_id}/take_off/{take_off_id}', [DupaPerProjectController::class, 'dupaListForTakeoffSelect']);
Route::get('dupa-list-by-group/{b3_project_id}', [DupaPerProjectController::class, 'showByProjectID']);
Route::resource('dupa-per-project', DupaPerProjectController::class);

Route::resource('dupa-content-per-project', DupaContentPerProjectController::class);
Route::post('dupa-content-per-project/minor-tool-percentage/{dupa_per_project}', [DupaContentPerProjectController::class, 'addMinorToolsPercentage']);
Route::delete('dupa-content-per-project/minor-tool-percentage/{dupa_per_project}', [DupaContentPerProjectController::class, 'deleteMinorToolsPercentage']);
Route::post('dupa-content-per-project/consumable-percentage/{dupa_per_project}', [DupaContentPerProjectController::class, 'addConsumablePercentage']);
Route::delete('dupa-content-per-project/consumable-percentage/{dupa_per_project}', [DupaContentPerProjectController::class, 'deleteConsumablePercentage']);
Route::post('dupa-content-per-project/material-area/{dupa_per_project}', [DupaContentPerProjectController::class, 'inputMaterialArea']);
Route::delete('dupa-content-per-project/material-area/{dupa_per_project}', [DupaContentPerProjectController::class, 'deleteMaterialArea']);
Route::post('dupa-content-per-project/equipment-area/{dupa_per_project}', [DupaContentPerProjectController::class, 'inputEquipmentArea']);
Route::delete('dupa-content-per-project/equipment-area/{dupa_per_project}', [DupaContentPerProjectController::class, 'deleteEquipmentArea']);

Route::resource('dupa-material-per-project', DupaMaterialPerProjectController::class);

Route::resource('dupa-labor-per-project', DupaLaborPerProjectController::class);

Route::resource('dupa-equipment-per-project', DupaEquipmentPerProjectController::class);

Route::resource('dupa-per-project-group', DupaPerProjectGroupController::class);

Route::resource('district', DistrictController::class);

Route::resource('barangay', BarangayController::class);

Route::apiResource('communication', CommunicationController::class);
Route::resource('comms-category', CommunicationCategoryController::class);

Route::resource('site-inspection-report', SiteInspectionReportController::class);
Route::post('site-inspection-report/draft', [SiteInspectionReportController::class, 'saveAsDraft']);
Route::post('site-inspection-report/submit', [SiteInspectionReportController::class, 'submit']);

Route::resource('b1-project-identification', B1ProjectIdentificationController::class);
Route::post('b1-project-identification/draft', [B1ProjectIdentificationController::class, 'saveAsDraft']);
Route::post('b1-project-identification/submit', [B1ProjectIdentificationController::class, 'submit']);

Route::controller(LOPEController::class)->name('lope.')->prefix('lope/')->group(function () {
    Route::get('{b3_project}', 'index')->name('index');
    Route::post('store/{b3_project}', 'store')->name('store');
    Route::get('show/{lope}', 'show')->name('show');
    Route::put('{lope}', 'update')->name('update');
    Route::delete('{lope}', 'destroy')->name('destroy');
});

Route::controller(LOMEController::class)->name('lome.')->prefix('lome/')->group(function () {
    Route::get('{b3_project}', 'index')->name('index');
    Route::post('store/{b3_project}', 'store')->name('store');
    Route::get('show/{lome}', 'show')->name('show');
    Route::put('{lome}', 'update')->name('update');
    Route::delete('{lome}', 'destroy')->name('destroy');
});

Route::controller(MERController::class)->name('mer.')->prefix('mer/')->group(function () {
    Route::get('{b3_project}', 'index')->name('index');
    Route::post('store/{b3_project}', 'store')->name('store');
    Route::get('show/{mer}', 'show')->name('show');
    Route::put('{mer}', 'update')->name('update');
    Route::delete('{mer}', 'destroy')->name('destroy');
});

Route::post('schedule/start-schedule', [ScheduleController::class, 'storeStartSchedule']);

//B3 Project For Approval
Route::put('b3-project/{b3_project}/for-approval', SendForApprovalController::class)->name('b3-project.for-approval');

// });

//PROJECT PLAN
Route::prefix('project-plan/')->name('project-plan.')->controller(ProjectPlanController::class)->group(function () {
    Route::get('file/{b3Project}/{projectPlanFile}', 'file')->name('file');
    Route::post('store', 'store')->name('store');
    Route::post('upload', 'upload')->name('upload');
    Route::delete('revert', 'revert')->name('revert');
    Route::post('upload-temporary-files', 'uploadTemporaryFiles')->name('uploadTemporaryFiles');
});

//B3 PROJECT PLAN
Route::prefix('project-plan/')->name('project-plan.')->controller(B3ProjectPlanController::class)->group(function () {
    Route::get('b3-project/{b3Project}', 'b3ProjectPlan')->name('b3ProjectPlan');
});