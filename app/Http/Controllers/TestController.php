<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestController extends Controller
{
    public function index()
    {
        return Test::all();
    }

    public function store(Request $request)
    {
//
    }

    public function show($id)
    {
        return Test::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        $row = Test::findOrFail($id);
        $row->delete();
    }
}
