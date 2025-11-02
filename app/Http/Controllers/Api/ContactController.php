<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Http\Traits\ApiResponserTrait;

class ContactController extends Controller
{
    use ApiResponserTrait;
    //
    public function contact_save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);


        $contact = Contact::create($request->only('name', 'email', 'message'));
        dd($request->all());

        return $this->successResponse($contact, 'Contact message submitted successfully.', 201);
    }
}
