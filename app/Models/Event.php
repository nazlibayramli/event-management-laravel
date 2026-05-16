<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $guarded = []; // Mass assignment icazəsi

    public function categories() { 
        return $this->hasMany(TicketCategory::class); 
    }
}