<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectPlan\StoreOrUpdateProjectPlanRequest;
use App\Models\B3Projects;
use App\Models\ProjectPlan;
use App\Models\ProjectPlanFile;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectPlanController extends Controller
{
    public function index(B3Projects $b3Project) 
    {
        return ProjectPlan::collection($b3Project->projectPlan);
    }

    public function store(StoreOrUpdateProjectPlanRequest $request)
    {
        try {
            if ($request->project_plans) {

                if (is_array($request->project_plans)) {
                    $files = [];

                    foreach ($request->project_plans as $project_plan) {
                        
                        $createdProjectPlan = ProjectPlan::updateOrCreate(
                            // Attributes to check for existing record
                            [
                                'id' => $project_plan['id'] ?? null,
                            ],
                            // Values to update or create the record with
                            [
                                'b3_project_id'  => $request->b3_project_id,
                                'name' => $project_plan['name']
                            ]
                        );
                        
                        $projectPlanId = $createdProjectPlan->id;

                        if (count($project_plan['filepond']) > 0) {

                                foreach ($project_plan['filepond'] as $file) {

                                    //IF FILE IS OBJECT = EXISTING | UPDATE
                                    if (is_string($file) && isset($project_plan['id'])) {
                                        $projectPlanFile = ProjectPlanFile::where('project_plan_id', $projectPlanId)->delete();
                                        
                                        if ($projectPlanFile) {
                                            
                                            $directoryPath = 'public/b3_project/' . $request->b3_project_id . '/project_plan/' . $project_plan['id'];
                                            
                                            if (Storage::exists($directoryPath)) {
                                                Storage::deleteDirectory('public/b3_project/' . $request->b3_project_id . '/project_plan/' . $project_plan['id']);
                                            } 

                                        }
                                    }

                                    $file_name = $this->storeFile($request->b3_project_id, $file, $projectPlanId);

                                    if($file_name != '') {
                                        $files[] = [
                                            'project_plan_id' => $projectPlanId,
                                            'filename' => $file_name,
                                            'created_at' => now()
                                        ];
                                    }
                            
                                }

                        }

                    }

                    ProjectPlanFile::insert($files);

                }

            }
            return [
                'code' => 200,
                'data' => [
                    'status' => 'success', 
                    'message' => 'Project Plan Successfully Imported'
                ]
            ];
                    
        } catch (\Throwable $th) {
            info("Error storing project plans: " . $th->getMEssage());
            return [
                'code' => 500,
                'data' => [
                    'status' => 'error', 
                    'message' => $th->getMessage()
                ]
            ];
        }
    }

    public function upload(Request $request){

        try {

            if ($request->hasFile('filepond')) {


                $file = $request->file('filepond');
                $original = $file->getClientOriginalName();
                $extension = $file->extension();
    
                $filename = \uniqid() . '-' . now()->timestamp . '.' . $extension;
                $folder = \uniqid() . '-' . now()->timestamp;
    
    
                $file = Storage::putFileAs('uploads/tmp/' . $folder, $file, $filename);
    
                if (!$file) // Will not create if storeAs fails
                    return response()->json(['status' => 'failed', 'message' => "Something's wrong in Storing File(s)"]);

               TemporaryFile::create([
                    'filename' => $filename,
                    'folder' => $folder,
                    'original' => $original,
                ]);

                return $folder;
            }

            return '';

        } catch (\Throwable $th) {
            info('Temporary file upload error: ' . $th->getMessage());
            return '';
        }

    }
    public function revert(Request $request)
    {

        if($request) {
            TemporaryFile::where('folder', $request->folder)->delete();
            Storage::deleteDirectory('temp/'.$request['folder']);
            
            return;
        }

        return response()->json(['message' => 'Server Error'], 500);
    }

    public function storeFile($b3ProjectId, $image_folder, $projectPlanId) : string
    {
        $file_data = TemporaryFile::where('folder', $image_folder)->first();
        
        if($file_data)
        {
            $folder = $file_data->folder;
            $old_filename = $file_data->filename;
            $new_filename = Uuid::uuid4() . '-' . $old_filename;
            Storage::move(
                'uploads/tmp/' . $folder .  '/' . $old_filename,
                'public/b3_project/' . $b3ProjectId . '/project_plan/' . $projectPlanId .'/'. $new_filename
            );
            
            Storage::deleteDirectory('uploads/tmp/' . $file_data->folder);
            
            return $new_filename;
        }

        return '';
        
    }

    public function file(B3Projects $b3Project, ProjectPlanFile $projectPlanFile)
    {
        try{             
            if(Storage::exists('public/b3_project/' . $b3Project->id)) {
                return response()->file(storage_path('app/public/b3_project/' . $b3Project->id . '/project_plan/' . $projectPlanFile->project_plan_id . '/' . $projectPlanFile->filename));
            }

            return response()->json(['message' => 'File not found.', 404]);

        } catch (\Throwable $th) {
            info('Retrieving Project Plan Uploaded File Error: ' . $th->getMessage());
            return response()->json(['message' => 'There was an error retrieving project plan file'], 500);
        }

    }

    public function uploadTemporaryFiles(Request $request)
    {
        if ($request->hasFile('filepond.*')) {
            $folders = [];
            $files = $request->file('filepond');
            foreach ($files as $file) {
                $original = $file->getClientOriginalName();
                $extension = $file->extension();

                $filename = \uniqid() . '-' . now()->timestamp . '.' . $extension;
                $folder = \uniqid() . '-' . now()->timestamp;

                $folders[] = $folder;

                $file = Storage::putFileAs('uploads/tmp/' . $folder, $file, $filename);

                if (!$file) // Will not create if storeAs fails
                    return response()->json(['status' => 'failed', 'message' => "Something's wrong in Storing File(s)"]);

                TemporaryFile::create([
                    'folder' => $folder,
                    'filename' => $filename,
                    'original' => $original,
                ]);
            }

            return $folders;
        } 
    }
}
