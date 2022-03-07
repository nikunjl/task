<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
	use SoftDeletes;
    use HasFactory;
    
    protected $table = 'employee';

    protected $dates = ['deleted_at'];

    public function companies()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
