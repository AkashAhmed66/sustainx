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
        'image_path',
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

    /**
     * Get the images for this subsection.
     */
    public function images()
    {
        return $this->hasMany(SubsectionImage::class)->orderBy('order_no');
    }

    /**
     * Get the image URL (for backward compatibility).
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return \Storage::url($this->image_path);
        }
        return null;
    }

    /**
     * Delete the image when the model is deleted.
     */
    protected static function booted()
    {
        static::deleted(function ($subsection) {
            if ($subsection->image_path && \Storage::exists($subsection->image_path)) {
                \Storage::delete($subsection->image_path);
            }
        });
    }
}
