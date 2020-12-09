<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ApiContactController extends Controller
{
    public function index()
    {
        return Contact::all();
    }

    public function store(Request $request)
    {
        $data = Contact::insert($request->all());
    }

    public function show($id)
    {
        return Contact::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = Contact::findOrFail($id)->update($request->all());
    }

    public function destroy($id)
    {
        $row = Contact::findOrFail($id);
        $row->delete();
    }
}
