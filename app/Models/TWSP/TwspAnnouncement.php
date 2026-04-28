<?php

namespace App\Models\TWSP;

use Illuminate\Database\Eloquent\Model;

class TwspAnnouncement extends Model
{
    protected $fillable = [
        'program_name',
        'total_slots',
        'filled_slots',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRemainingSlots()
    {
        return $this->total_slots - $this->filled_slots;
    }

    public function hasAvailableSlots()
    {
        return $this->getRemainingSlots() > 0;
    }

    public static function getActive()
    {
        $announcement = self::where('is_active', true)->first();
        
        if ($announcement && $announcement->hasAvailableSlots()) {
            return $announcement;
        }
        
        return null;
    }

    public function incrementFilledSlots()
    {
        $this->filled_slots++;
        
        if ($this->filled_slots >= $this->total_slots) {
            $this->is_active = false;
        }
        
        $this->save();
    }
}
