<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'name',
        'description',
        'order_no',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the section that owns the subsection.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the items for the subsection.
     */
    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('order_no');
    }
}
