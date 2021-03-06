<?php

namespace App;

use App\Models\FavouriteMarkers;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
//        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
        return config('app.url') . '/user/get/avatar/' . $this->id;
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function orders()
    {
        return $this->belongsToMany('App\Models\Orders', 'orders');
    }

    public function favouriteMarkers()
    {
        return $this->belongsToMany('App\Models\Marker', 'favourite_markers');
    }

    public function favouriteTours()
    {
        return $this->belongsToMany('App\Models\Tour', 'favourite_tours');
    }

    public static function pagination($search_query, $order_by, $per_page)
    {
       return User::select(['id', 'name', 'email', 'telephone', 'active', 'created_at', 'updated_at'])
            ->when($search_query, function ($q) use ($search_query) {
                $q ->where('name', 'LIKE', '%' . $search_query . '%');
            })
            ->orderBy('created_at', $order_by)
            ->paginate($per_page);
    }
}
