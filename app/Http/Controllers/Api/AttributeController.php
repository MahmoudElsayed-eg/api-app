<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAttributeRequest;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Attributes
 */
class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(CreateAttributeRequest $request)
    {
        $user = $request->user();
        if (! $user->projects()->where('projects.id', $request->project_id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Start transaction to prevent partial inserts
        DB::beginTransaction();

        try {
            $validated = $request->validated(); 

            // Create Attribute
            $attribute = Attribute::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'entity_id' => $validated['project_id'],
            ]);

            // Create AttributeValue
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'entity_id' => $validated['project_id'],
                'value' => $validated['value'],
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'Attribute created successfully',
                'attribute' => new AttributeResource($attribute)
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json(['message' => 'Failed to create attribute', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified attribute.
     */
    public function show(Request $request,Attribute $attribute)
    {
        $user = $request->user();
        if (! $user->projects()->where('projects.id', $attribute->entity_id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new AttributeResource($attribute);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified attribute in storage.
     */
    public function update(CreateAttributeRequest $request, Attribute $attribute)
    {
        $user = $request->user();
        // prevent user from updating if attribue not in his project and project_id not in his projects 
        if ($user->projects()->whereIn('projects.id', [$request->project_id, $attribute->entity_id])->count() !== 2) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated(); 

            $attribute->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'entity_id' => $validated['project_id'],
            ]);

            $attribute->value()->update([
                'entity_id' => $validated['project_id'],
                'value' => $validated['value'],
            ]);


            return response()->json([
                'message' => 'Attribute updated successfully',
                'attribute' => new AttributeResource($attribute)
            ]);

        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json(['message' => 'Failed to update attribute', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Attribute $attribute)
    {
        $user = $request->user();
        if (! $user->projects()->where('projects.id', $attribute->entity_id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $attribute->delete();
        return response()->json(['message' => 'attribute deleted successfully'], 200);
    }
}
