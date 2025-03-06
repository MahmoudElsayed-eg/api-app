<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTimeSheetRequest;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\QueryParam;

/**
 * @group Time sheets
 */
class TimesheetController extends Controller
{
    /**
     * Display a listing of the timesheet.
     */
    #[QueryParam("task_name", "string", "Filter by task name.", required: false)]
    #[QueryParam("date", "string", "Filter by specific date (YYYY-MM-DD).", required: false)]
    #[QueryParam("hours", "integer", "Filter by number of hours.", required: false)]
    #[QueryParam("project_id", "integer", "Filter by project ID.", required: false)]
    public function index(Request $request)
    {
        $user = $request->user();
        $timesheets = $user->timesheets()->filter($request->except('page'))->paginate(10);

        return response()->json([
            'timesheets' => $timesheets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created timesheet in storage.
     */
    public function store(CreateTimeSheetRequest $request)
    {
        $user = $request->user();
        if (!$user->projects()->where('projects.id', $request->project_id)->exists()) {
            return response()->json(['message' => 'Unauthorized: You do not own this project'], 403);
        }
        // Create the sheet
        $sheet = Timesheet::create([...$request->validated(), 'user_id' => $user->id]);

        return response()->json([
            'message' => 'sheet created successfully',
            'sheet' => $sheet,
        ], 201);
    }

    /**
     * Display the specified timesheet.
     */
    public function show(Timesheet $timesheet)
    {
        if (!Gate::allows('view', $timesheet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json([
            'timesheet' => $timesheet
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified timesheet in storage.
     */
    public function update(CreateTimeSheetRequest $request, Timesheet $timesheet)
    {
        if (!Gate::allows('update', $timesheet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $timesheet->update($request->validated());
        return response()->json([
            'message' => 'timesheet updated successfully',
            'timesheet' => $timesheet,
        ]);
    }

    /**
     * Remove the specified timesheet from storage.
     */
    public function destroy(Timesheet $timesheet)
    {
        if (!Gate::allows('delete', $timesheet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $timesheet->delete();
        return response()->json(['message' => 'timesheet deleted successfully'], 200);
    }
}
