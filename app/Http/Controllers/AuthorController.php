<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;

class AuthorController extends Controller
{
    public function index()
    {
        return Author::all();
    }

    public function store(Request $request)
    {
//
    }

    public function show($id)
    {
        return Author::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        $row = Author::findOrFail($id);
        $row->delete();
    }
}
