<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrediccionQuiniela extends Controller
{
    function index()
    {
        return view('prediccion-quiniela');
    }

    function store(Request $request)
    {
        $request->validate([
            'partido_id' => 'required|exists:partidos,id',
            'signo' => 'required|in:0,1,2',
        ]);

        $user = auth()->user();
        $partidoId = $request->input('partido_id');
        $signo = $request->input('signo');

        // Aquí puedes agregar la lógica para guardar la predicción en la base de datos
        // Por ejemplo:
        // Prediccion::create([
        //     'user_id' => $user->id,
        //     'partido_id' => $partidoId,
        //     'signo' => $signo,
        // ]);

        return response()->json(['message' => 'Predicción guardada correctamente']);
    }

    function show($id)
    {
        // Aquí puedes agregar la lógica para mostrar una predicción específica
        // Por ejemplo:
        // $prediccion = Prediccion::findOrFail($id);
        // return view('prediccion-detalle', compact('prediccion'));

        return response()->json(['message' => 'Mostrar predicción con ID: ' . $id]);
    }
    

}
