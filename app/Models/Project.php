<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $guarded = false;
    protected $table = 'projects';
    protected $with = ['type'];

    public function type()
    : BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
