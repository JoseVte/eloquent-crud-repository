<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModelWithSoftDelete extends Model
{
    use SoftDeletes;

    protected $fillable = ['msg'];
}
