<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Knuckles\Scribe\Attributes\QueryParam;

/**
 * @group Projects
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    #[QueryParam("filters", "array", "Dynamic key-value filters for any project attribute.", required: false, example: ["status" => "active", "priority" => "high"])]
    public function index(Request $request)
    {

        $user = $request->user();
        $filters = $request->query('filters', []);
        $projects = $user->projects()->with('attributes.value')->filter($filters)->paginate(10);

        return ProjectResource::collection($projects);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(CreateProjectRequest $request)
    {
        DB::beginTransaction(); // Start transaction

        try {
            $user = $request->user();

            // Create the project with validated data
            $project = $user->projects()->create([
                'name' => $request->validated('name'),
                'status' => $request->validated('status'),
            ]);

            // Check if attributes exist and store them
            if ($request->validated('attributes') && count($request->validated('attributes')) > 0) {
                foreach ($request->validated('attributes') as $attributeData) {
                    $attribute = $project->attributes()->create([
                        'name' => $attributeData['attr']['name'],
                        'type' => $attributeData['attr']['type'],
                        'entity_id' => $project->id
                    ]);

                    // Create the attribute value
                    $attribute->value()->create([
                        'value' => $attributeData['value']['value'],
                        'entity_id' => $project->id
                    ]);
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'message' => 'Project created successfully',
                'project' => new ProjectResource($project)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback changes if any operation fails

            return response()->json([
                'message' => 'Project creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {

        if (!Gate::allows('view', $project)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new ProjectResource($project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified project in storage.
     */
    public function update(CreateProjectRequest $request, Project $project)
    {
        // Authorization check
        if (!Gate::allows('update', $project)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction(); // Start transaction

        try {
            // Update project details
            $project->update([
                'name' => $request->validated('name'),
                'status' => $request->validated('status'),
            ]);

            // Process attributes
            if ($request->validated('attributes') && count($request->validated('attributes')) > 0) {
                foreach ($request->validated('attributes') as $attributeData) {
                    // Find or create the attribute
                    $attribute = $project->attributes()->updateOrCreate(
                        ['name' => $attributeData['attr']['name'], 'entity_id' => $project->id], // Find by name & entity_id
                        [
                            'type' => $attributeData['attr']['type'],
                        ]
                    );

                    // Update or create the attribute value
                    $attribute->value()->updateOrCreate(
                        ['attribute_id' => $attribute->id, 'entity_id' => $project->id],
                        ['value' => $attributeData['value']['value']]
                    );
                }
            }

            DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Project updated successfully',
                'project' => new ProjectResource($project)
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback changes on failure

            return response()->json([
                'message' => 'Project update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $user = $request->user();

        if (!Gate::allows('delete', $project)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction(); // Start transaction

        try {
            // Delete related attributes and values
            $project->attributes()->delete();
            $project->attribute_values()->delete();

            // Detach the project from the user (if using Many-to-Many)
            $user->projects()->detach($project->id);

            $project->delete();

            DB::commit(); // Commit transaction

            return response()->json(['message' => 'Project deleted successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback changes on failure

            return response()->json([
                'message' => 'Project deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
