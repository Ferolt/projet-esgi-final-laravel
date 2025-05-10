<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskColumn;

class TaskColumnController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        TaskColumn::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Colonne ajoutée');
    }
}
