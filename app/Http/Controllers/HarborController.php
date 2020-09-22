<?php

namespace App\Http\Controllers;

use App\Harbor;
use Illuminate\Http\Request;

class HarborController extends Controller
{
    public function search(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $harbors = Harbor::where('varation->type', 'like', '%' . $term . '%')->get();

        $formatted_harbors = [];
        foreach ($harbors as $harbor) {
            $formatted_harbors[] = ['id' => $harbor->id_complete, 'text' => $harbor->display_name];
        }
        return \Response::json($formatted_harbors);
    }
}
