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

        $harbors = Harbor::where('varation->type', 'like', '%' . strtolower($term) . '%')->get();

        $formatted_harbors = [];
        foreach ($harbors as $harbor) {
            $formatted_harbors[] = ['id' => $harbor->id_complete, 'text' => $harbor->display_name];
        }
        return \Response::json($formatted_harbors);
    }

    public function loadviewChild($id)
    {
        $parent = Harbor::where('id', $id)->first();
        $select = Harbor::where('id', '!=', $id)->where('harbor_parent', $id)->pluck('id');
        
        if($parent->hierarchy =='parent'){
            $harbor = Harbor::where('harbor_parent', NULL)->orwhere('harbor_parent',$id)->where('hierarchy', '!=','parent')->get();
        
            $harbor->pull($id);
            $harbor = $harbor->where('hierarchy', '!=','parent')->pluck('name', 'id');
        }else{
            $harbor = Harbor::where('hierarchy', 'none')->where('id','!=' , $id)->pluck('name', 'id');

        }
        
        return view('harbors.Body-Modals.child', compact('harbor', 'select', 'parent'));

    }

    public function storeHierarchy(Request $request)
    {

        $harborParent = $request->parent;
        $harborChild = $request->child;
        $harborClean = Harbor::where('harbor_parent', $harborParent)->get();
        // Primero limpiamos la data actual
        if (!$harborClean->isEmpty()) {
            foreach ($harborClean as $clean) {
                $clean->hierarchy = 'none';
                $clean->harbor_parent = null;
                $clean->update();
            }
        }
        // Si los harbors child vienen en blanco limpiamos al padre
        if (empty($harborChild)) {
            $harborClean = Harbor::where('id', $harborParent)->first();
            $harborClean->hierarchy = 'none';
            $harborClean->harbor_parent = null;
            $harborClean->update();
        } else {
            $harborParentupdate = Harbor::where('id', $harborParent)->first();
            $harborParentupdate->hierarchy = 'parent';
            $harborParentupdate->harbor_parent = null;
            $harborParentupdate->update();

            foreach ($harborChild as $child) {

                $harborupdate = Harbor::where('id', $child)->first();
                $harborupdate->hierarchy = 'child';
                $harborupdate->harbor_parent = $harborParent;
                $harborupdate->update();

            }

        }
        // Ahora guardamos los nuevo cambios
        return response()->json(['success' => true]);

    }

}
