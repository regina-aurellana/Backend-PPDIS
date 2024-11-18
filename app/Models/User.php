<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Team;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'position',
        'password',
        'is_active',
        'role_id',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class,  'id', 'team_id');
    }

    public function communicationRoutedTo()
    {
        return $this->hasMany(CommunicationContent::class, 'routed_to_user_id', 'id');
    }

    public function communicationRoutedBy()
    {
        return $this->hasMany(CommunicationContent::class, 'routed_by_user_id', 'id');
    }
}
