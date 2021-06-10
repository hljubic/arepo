<?php

namespace App\Http\Controllers;

use App\Services\CarnetMatica\CarnetMaticaService;
use Illuminate\Http\Request;

class GroupController extends ResourceController
{
    protected static $modelName = 'Group';

    public function getDepartmentsFromCarnet(Request $request, CarnetMaticaService $carnetMaticaService)
    {
        $validated = $request->validate([
            'Sifra' => 'required|string',
            'Podsifra' => 'required|string'
        ]);
        // TODO: treba povuć ID akademske godine i ostalo....

        return $carnetMaticaService->getDepartments($validated['Sifra'], $validated['Podsifra']);

    }
}
