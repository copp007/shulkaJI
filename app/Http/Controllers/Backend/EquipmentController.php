<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request; // Correct import for Request
use Illuminate\Support\Facades\Http; // Import the HTTP facade

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Equipment_details;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index()
    {
        //$this->checkAuthorization(auth()->user(), ['dashboard.view']);

        // return view(
        //     'backend.pages.equipment.index',
        //     [
        //         'total_admins' => Admin::count(),
        //         'total_roles' => Role::count(),
        //         'total_permissions' => Permission::count(),
        //     ]
        // );

        $equipment = Equipment_details::all();

        return view('backend.pages.equipment.index', [
            'equipments' => Equipment_details::all(),
        ]);
    }

    public function map()
    {
       
        $equipment = Equipment_details::all();

        return view('backend.pages.equipment.map', [
            'equipments' => Equipment_details::all(),
        ]);
    }


    public function recordAudio(){
        return view('backend.pages.equipment.recordAudio');
    }

    public function uploadAudio(Request $request)
    {

        // Validate the incoming request
        // $request->validate([
        //     'audio' => 'required|file|mimes:mp3,wav,ogg|max:10240', // Adjust max size as needed
        // ]);

        // Store the uploaded file
        $path = $request->file('audio')->store('public/audio');



        // Return a JSON response
        return response()->json(['success' => 'Audio uploaded successfully', 'path' => $path]);
    }


   public function playTone(Request $request)
    {
        
        $id = $request->query('id');

       
        if (!$id || !is_numeric($id)) {
            return response()->json(['error' => 'Invalid ID'], 400);
        }

       
        $equipment = Equipment_details::find($id);

       
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }

        
       // Fetch the URL from the equipment record
        $url = $equipment->url;

        // Perform a cURL request to the fetched URL
        try {
            $response = Http::get($url);

            // Return the response from the URL or handle it as needed
            return response()->json([
                'status' => 'success',
                'data' => $response->json(), // Assuming the response is JSON
            ]);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json([
                'error' => 'Failed to make a request to the URL',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
