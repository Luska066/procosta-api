<?php

use App\Models\Bounds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Storage;

Route::middleware('auth:api')->group(function () {
    Route::post('/image', function (Request $request) {
       return $request->user();
    });
    Route::post('/upload/files', function (Request $request) {
        $type = $request->query('type') ?? 'temp';
        $file = $request->file('file');
        $pah = $file->storeAs($type, $file->getClientOriginalName(),'public');
        return [
            "hasFile" => $request->hasFile('file'),
            "path" => $pah
        ];
    });
    Route::post('/upload/bounds', function (Request $request) {
        $request->validate([
           'min_lat' => 'required',
           'max_lat' => 'required',
           'min_lon' => 'required',
           'max_lon' => 'required',
           'min' => 'required',
           'max' => 'required',
        ]);
        $type = $request->query('type') ?? "temp";
        $bounds =  Bounds::query();
        $id = 1;
        switch ($type) {
            case "temp":
               $bounds->updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'min_lat' => $request->get('min_lat'),
                        'max_lat' => $request->get('max_lat'),
                        'min_lon' => $request->get('min_lon'),
                        'max_lon' => $request->get('max_lon'),
                        'temp_min' => $request->get('min'),
                        'temp_max' => $request->get('max'),
                    ]
                );
                break;
            case "salt":
                $bounds->updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'salt_min' => $request->get('min'),
                        'salt_max' => $request->get('max'),
                    ]
                );
                break;
            case "w":
                $bounds->updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'w_min' => $request->get('min'),
                        'w_max' => $request->get('max'),
                    ]
                );
                break;
            case "rain":
                $bounds->updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'rain_min' => $request->get('min'),
                        'rain_max' => $request->get('max'),
                    ]
                );
                break;
            default:
                $bounds->updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                        'min_lat' => $request->get('min_lat'),
                        'max_lat' => $request->get('max_lat'),
                        'min_lon' => $request->get('min_lon'),
                        'max_lon' => $request->get('max_lon'),
                    ]
                );
        }

    });
});

Route::get('/bounds', function () {
    $bounds = Bounds::query()->first();
    return [
        [round($bounds->min_lat, 4)+0.2, round($bounds->min_lon, 4)],
        [round($bounds->max_lat, 4)+0.2, round($bounds->max_lon, 4)],
    ];
});

Route::get('/image/temp', function (Request $request) {
    $payload = [];
    $type = $request->query('type') ?? 'temp';

    foreach ( Storage::disk('public')->allFiles($type) as $file){
        $file_name = basename($file);
        $id = explode('_', basename($file_name))[1];
        $payload[] = [
            "id" => explode('.', basename($id))[0],
            "filename" => $file_name,
            "path" => Storage::disk('public')->url($file)
        ];
    }
   return  response($payload)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');;
});

Route::get('/image/wind/{fileName}', function (Request $request,$fileName) {
    $payload = [];
    $type = $request->query('type') ?? 'w';
    
    $json = Storage::disk('public')->get($type."/$fileName");
   return json_encode($json,false);
});


