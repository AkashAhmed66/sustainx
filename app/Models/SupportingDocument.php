<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SupportingDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_id',
        'item_id',
        'subsection_id',
        'file_name',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * Get the assessment that owns the document.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the question entity that owns the document.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the item that owns the document.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the subsection that owns the document.
     */
    public function subsection()
    {
        return $this->belongsTo(Subsection::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the file when the model is deleted.
     */
    protected static function booted()
    {
        static::deleted(function ($document) {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }
}
