<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public static function uploadFile(Request $request, $keep_name = false)
    {
        $file = $request->file('file');

        if ($keep_name) {
            if (!$filename = self::saveFileToStorage($file, $file->getClientOriginalName())) {
                $parts = explode('.', $file->getClientOriginalName());

                $filename = self::saveFileToStorage($file, ($keep_name ? $parts[0] . '_' : '') . time() . '.' . $file->getClientOriginalExtension());
            }
        } else {
            $filename = self::saveFileToStorage($file, time() . '.' . $file->getClientOriginalExtension());
        }

        if ($filename == null && !$filename = self::saveFileToStorage($file, time() . '_' . rand(10, 10 ** 6) . '.' . $file->getClientOriginalExtension())) {
            abort(484, 'Ne mogu pohraniti datoteku s tim imenom! Molimo preimenujte je.');
        }

        return $filename;
    }

    public static function saveFileToStorage($file, $filename)
    {
        if (file_exists(storage_path() . '/app/public/' . $filename))
            return null;

        $file->move(storage_path() . '/app/public/', $filename);

        return $filename;
    }

}
