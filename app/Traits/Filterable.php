<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Filterable
{
    public function scopeFilter($query, array $filters = [], array $relationFilters = [], $relationName = '')
    {
        // Call magija function to build query for each field
        // and attach it to existing one
        foreach ($filters as $column => $value) {
            if (Schema::hasColumn($this->getTable(), $column))
                $query->magija($column, $value, $relationName);
        }

        // For each relation filter, attach whereHas depending of relation
        foreach ($relationFilters as $relation => $filterColumns) {
            if (is_array($filterColumns)) {
                foreach ($filterColumns as $key => $values) {
                    if (is_array($values)) {
                        $query->whereHas($relation, function ($query2) use ($values, $relation) {
                            $query2->filter($values, [], $relation);
                        });
                    } else {
                        // Olaksica ako hoce da dobavi one entitete koji moraju imati 2 ili vise uvjeta,a ne whereIn. Primjer:
                        // filterRelation[ads][0][id]=1 i filterRelation[ads][0][id]=2
                        // Ovakoako moze samo filterRelation[ads][id]=a=1,2
                        if (!is_array($values) && Str::startsWith($values, 'a=')) {
                            $valueWithoutPrefix = Str::replaceFirst('a=', '', $values);
                            $values = array_filter(explode(",", $valueWithoutPrefix));
                        }
                        $values = (array)$values;
                        foreach ($values as $value) {
                            $parameter = [
                                $key => $value
                            ];
                            $query->whereHas($relation, function ($query2) use ($parameter, $relation) {
                                $query2->filter($parameter, [], $relation);
                            });
                        }
                    }

                }
            }
        }
        return $query;
    }

    public function scopeMagija($query, $column, $value, $relation = null)
    {
        // Ovo template onaj koristi ako cemo da nam bude jos naprednije.
        // Kao npr na LIKE da dodamo %?%.
        $operators = [
            '<>' => ['case' => 'whereBetween', 'operator' => null, 'template' => 'array'],
            'v>' => ['case' => 'where', 'operator' => '>', 'template' => ''],
            'v<' => ['case' => 'where', 'operator' => '<', 'template' => ''],
            't>' => ['case' => 'where', 'operator' => '>', 'template' => 'timestamp'],
            't<' => ['case' => 'where', 'operator' => '<', 'template' => 'timestamp'],
            'n=' => ['case' => 'whereNull', 'operator' => null, 'template' => ''],
            'n!=' => ['case' => 'whereNotNull', 'operator' => null, 'template' => ''],
            'i=' => ['case' => 'whereIn', 'operator' => null, 'template' => 'array'],
            'i!=' => ['case' => 'whereNotIn', 'operator' => null, 'template' => 'array'],
            'd=' => ['case' => 'whereDate', 'operator' => null, 'template' => 'date'],
            'd>' => ['case' => 'whereDate', 'operator' => '>', 'template' => 'date'],
            'd<' => ['case' => 'whereDate', 'operator' => '<', 'template' => 'date'],
            '~=' => ['case' => 'where', 'operator' => 'like', 'template' => '%value%'],
        ];
        $default = true;

        // Ako nema ovog onda bi polje moglo biti ambigious. Ako postoji relacija ovo ce samo nadodat u SQL-u relacija.ime_columne
        if ($relation) {
            $tableName = $this->getTable();
            $column = $tableName . '.' . $column;
        }

        // Prodemo kroz sve operatore i gledamo da li ijedan value columne započinje sa nekim od njih
        foreach ($operators as $operator => $rules) {

            $result = Str::startsWith($value, $operator);
            // Ako smo nabasali na neki onda idemo nakačit na taj query (kojeg smo primili kao prvi parametar)
            if ($result) {
                $default = false;
                $case = $rules['case'];
                $op = $rules['operator'];
                $template = $rules['template'];

                // Sa columne sklonimo operator da ne smeta. Znaci s pocetka stringa skinemo ovaj 't>', 't<' itd
                $valueWithoutPrefix = Str::replaceFirst($operator, '', $value);

                if ($template == '%value%') {
                    $valueWithoutPrefix = str_replace("value", $valueWithoutPrefix, $template);
                }

                // Ako se radi o arrayu znaci da ce operator morat biti ili whereIn ili whereNotIn
                if ($template == 'array') {
                    $valueWithoutPrefix = array_filter(explode(",", $valueWithoutPrefix), 'strlen');
                }

                // Konacno na taj query nakačimo 'case' (npr. 'where'). Zatim proslijedimo ime columne, operator i value columne.
                // Ako nema operatora onda ga ne saljemo
                // ex.
                //  $case = 'where',
                // $column = 'created_at',
                // $op = '>'
                // $valueWithoutPrefix = '2020-05-05'
                // $query->where('created_at', '>', '2020-05-05')
                if ($case == 'whereNull' || $case == 'whereNotNull') {
                    $query->$case($column);
                } else if (is_null($op)) {
                    $query->$case($column, $valueWithoutPrefix);
                } else {
                    $query->$case($column, $op, $valueWithoutPrefix);
                }
                break;
            }
        }

        if ($default)
            $query->where($column, $value);

        return $query;
    }

}
