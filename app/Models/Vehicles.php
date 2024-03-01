<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Vehicles extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->select(
            'vehicles.*',
            'users.id as user_id',
            'users.name as user_name'
        )
            ->join('users', 'vehicles.user_id', 'users.id');

        if ($search && isset($search)) {
            $query = $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('model', 'like', '%' . $search . '%')
                ->orWhere('year', 'like', '%' . $search . '%')
                ->orWhere('plate', 'like', '%' . $search . '%');
        }

        return $query;
    }

    public function scopeGetUserVehicles($query, $id)
    {
        $query->select(
            'vehicles.*',
            'users.id as user_id',
            'users.name as user_name',
            'users.mail as user_mail',
        )
            ->join('users', 'vehicles.user_id', 'users.id')
        ->where('vehicles.id', $id);

        return $query;
    }
}
