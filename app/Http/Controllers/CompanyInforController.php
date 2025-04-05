<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyInfor;

class CompanyInforController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'company_name' => 'required|max:100',
            'address' => 'required|max:300',
            'director_name' => 'required|max:100',
            'founded_date' => 'nullable|date',
            'business_scope' => 'nullable|string',
            'capital' => 'nullable|string',
            'group_parent' => 'nullable|string',
            'group_subsidiaries' => 'nullable|array',
            'employee_count' => 'nullable|integer',
        ]);

        $company_info = CompanyInfor::create([
            'company_name' => $request->input('company_name'),
            'address' => $request->input('address'),
            'director_name' => $request->input('director_name'),
            'founded_date' => $request->input('founded_date'),
            'business_scope' => $request->input('business_scope'),
            'capital' => $request->input('capital'),
            'group_parent' => $request->input('group_parent'),
            'group_subsidiaries' => $request->input('group_subsidiaries'),
            'employee_count' => $request->input('employee_count'),
        ]);

        return response(['data' => $company_info], 201);
    }

    public function index() {
        $company_info = CompanyInfor::all();
        return response(['data'=> $company_info]);
    }

    public function show($id) {
        $company_info = CompanyInfor::findOrFail($id);
        return response()->json($company_info);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'company_name' => 'required|max:100',
            'address' => 'required|max:300',
            'director_name' => 'required|max:100',
            'founded_date' => 'nullable|date',
            'business_scope' => 'nullable|string',
            'capital' => 'nullable|string',
            'group_parent' => 'nullable|string',
            'group_subsidiaries' => 'nullable|array',
            'employee_count' => 'nullable|integer',
        ]);

        $company_info = CompanyInfor::findOrFail($id);

        $company_info->update([
            'company_name' => $request->input('company_name'),
            'address' => $request->input('address'),
            'director_name' => $request->input('director_name'),
            'founded_date' => $request->input('founded_date'),
            'business_scope' => $request->input('business_scope'),
            'capital' => $request->input('capital'),
            'group_parent' => $request->input('group_parent'),
            'group_subsidiaries' => $request->input('group_subsidiaries'),
            'employee_count' => $request->input('employee_count'),
        ]);

        return response(['data' => $company_info], 200);
    }

    public function destroy($id) {
        $company_info = CompanyInfor::findOrFail($id);
        $company_info->delete();

        return response()->json([
            'message' => 'Company information deleted successfully',
            'data' => $company_info
        ]);
    }
}
