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

    public function scopeSearch($query, $search, $user)
    {
        $query->select(
            'vehicles.*',
            'users.id as user_id',
            'users.name as user_name'
        )

            ->join('users', 'vehicles.user_id', 'users.id')
            ->orderBy('vehicles.id', 'desc');

        if ($search && isset($search)) {
            $query = $query->where('plate', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('renavam', 'like', '%' . $search . '%')
                ->orWhere('model', 'like', '%' . $search . '%')
            ->orWhere('year', 'like', '%' . $search . '%');
        }

        if ($user->role != User::ROLE_ADMIN) {
            $query = $query->where('vehicles.user_id', $user->id);
        }

        return $query;
    }

    public function scopeGetUserVehicles($query, $id)
    {
        $query->select(
            'vehicles.*',
            'users.id as user_id',
            'users.name as user_name',
            'users.email as user_email',
        )
            ->join('users', 'vehicles.user_id', 'users.id')
            ->where('vehicles.id', $id);

        return $query;
    }
}
