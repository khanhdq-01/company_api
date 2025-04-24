<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:65535',
        ]);

        // Save the data to the database
        $contact = Contact::create($validatedData);

        // Return a response
        return response()->json([
            'message' => 'User information saved successfully!',
            'data' => $contact,
        ], 201);
    }

    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied'
        ]);

        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();

        return response()->json([
            'message'=> 'Update status success',
            'data'=> $contact,
        ],200);
    }
}