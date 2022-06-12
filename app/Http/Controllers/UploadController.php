<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator,Redirect,Response,File;
Use App\Models\Image;
Use App\Models\Pdf;

class UploadController extends Controller
{
    public function uploadImage($request)
    {
        request()->validate([
            'fileUpload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($files = $request->file('fileUpload')) {
            $destinationPath = 'public/image/'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $insert['image'] = "$profileImage";
        }
        $check = Image::insertGetId($insert);

        return response()->json([
            'message' => 'Successfully uploaded!'
        ], 201);

    }

    public function uploadPdf($request)
    {
        request()->validate([
            'fileUpload' => 'required|mimes:pdf,xlx,csv|max:2048',
        ]);
        if ($files = $request->file('fileUpload')) {
            $destinationPath = 'public/image/'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $insert['pdf'] = "$profileImage";
        }
        $check = Pdf::insertGetId($insert);

        return response()->json([
            'message' => 'Successfully uploaded!'
        ], 201);

    }
}
