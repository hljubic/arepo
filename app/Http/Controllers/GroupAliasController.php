<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupAliasController extends ResourceController
{
    protected static $modelName = 'GroupAlias';


    public function destroy(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);

        if ($this->startsWith($item->name, 'admin@')
            || $this->startsWith($item->name, 'ured@')) {
            abort(413, 'Alis koji počinje sa admin@ ili ured@ ne može biti izbrisan!');
        }

        $item->delete();

        return response('Success', 204);
    }

    function startsWith ($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
