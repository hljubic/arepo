<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperStudentType
 */
class StudentType extends ResourceModel
{
    protected static $title = 'Vrste korisnika';

    protected $fillable = ['title'];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|string'
        ]
    ];}
