<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'imagem',        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',        
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comentarios()
    {
        return $this->hasMany('App\Models\Comentario');
    }
    public function conteudos()
    {
        return $this->hasMany('App\Models\Conteudo');
    }
    public function curtidas()
    {
        return $this->belongsToMany('App\Models\Conteudo', 'curtidas', 'user_id', 'conteudo_id');
    }
    public function amigos()
    {
        return $this->belongsToMany('App\Models\User', 'amigos', 'user_id', 'amigo_id');
    }
}
