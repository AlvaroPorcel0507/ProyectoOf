<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'name', 'description', 'scheduleDate', 'duration', 'priority', 'status', 'userId'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $activities = Activity::where('status', 1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/activities.index', compact('activities', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/activities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
            'description' => 'required|max:300|regex:/^[A-Za-z\d\p{L} ]+$/u',
            'scheduledDate' => 'required|date',
            'duration' => 'required|date',
            'priority' => 'required|integer',
            'idUser' => 'required|integer',
        ]);

        Activity::create([
            'name' => $request->name,
            'description' => $request->description,
            'scheduledDate' => $request->scheduledDate,
            'duration' => $request->duration,
            'priority' => $request->priority,
            'idUser' => $request->idUser,
        ]);

        return redirect()->route('activities.index')->with('success', 'Solicitud registrada exitosamente.');
    }

    public function edit(Activity $activity)
    {
        return view('livewire/activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
            'description' => 'required|max:300|regex:/^[A-Za-z\d\p{L} ]+$/u',
            'scheduledDate' => 'required|date',
            'duration' => 'required|date',
            'priority' => 'required|integer',
            'idUser' => 'required|integer',
        ]);

        $activity->update([
            'name' => $request->name,
            'description' => $request->description,
            'scheduledDate' => $request->scheduledDate,
            'duration' => $request->duration,
            'priority' => $request->priority,
            'idUser' => $request->idUser,
        ]);

        return redirect()->route('activities.index')->with('success', 'Solicitud actualizada correctamente.');
    }

    public function delete(Activity $activity)
    {
        $activity->update([
            'status' => 0
        ]);

        return redirect()->route('activities.index')->with('success', 'Solicitud Eliminada con exito.');
    }
}
