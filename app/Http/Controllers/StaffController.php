<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $currentDay = strtolower($request->get('day', 'viernes'));
        $query = Staff::where('is_active', true)->orderBy('name');

        if ($currentDay === 'viernes') {
            $query->where('works_friday', true);
        } elseif ($currentDay === 'sabado') {
            $query->where('works_saturday', true);
        } elseif ($currentDay === 'domingo') {
            $query->where('works_sunday', true);
        }

        $staffMembers = $query->get();

        $countViernes = Staff::where('is_active', true)->where('works_friday', true)->count();
        $countSabado = Staff::where('is_active', true)->where('works_saturday', true)->count();
        $countDomingo = Staff::where('is_active', true)->where('works_sunday', true)->count();
        $countTodos = Staff::where('is_active', true)->count();

        return view('staff.index', compact(
            'staffMembers',
            'currentDay',
            'countViernes',
            'countSabado',
            'countDomingo',
            'countTodos'
        ));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'default_pay' => 'required|numeric|min:0',
            'assigned_bar' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'friday_pay' => 'nullable|numeric|min:0',
            'saturday_pay' => 'nullable|numeric|min:0',
            'sunday_pay' => 'nullable|numeric|min:0',
        ]);

        $validated['works_friday'] = $request->boolean('works_friday', true);
        $validated['works_saturday'] = $request->boolean('works_saturday', true);
        $validated['works_sunday'] = $request->boolean('works_sunday', true);

        Staff::create($validated);

        return redirect()->route('staff.index');
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'default_pay' => 'required|numeric|min:0',
            'assigned_bar' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
            'friday_pay' => 'nullable|numeric|min:0',
            'saturday_pay' => 'nullable|numeric|min:0',
            'sunday_pay' => 'nullable|numeric|min:0',
        ]);

        $validated['works_friday'] = $request->boolean('works_friday');
        $validated['works_saturday'] = $request->boolean('works_saturday');
        $validated['works_sunday'] = $request->boolean('works_sunday');

        $staff->update($validated);

        return redirect()->route('staff.index');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index');
    }
}
