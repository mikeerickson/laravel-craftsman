<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use {{model_path}};

class {{name}} extends Controller
{
    public function index()
    {
    return {{model}}::all();
    }

    public function store(Request $request)
{
//
}

    public function show($id)
{
    return {{model}}::findOrFail($id);
    }

    public function destroy($id)
{
    $row = {{model}}::findOrFail($id);
        $row->delete();
    }
}
