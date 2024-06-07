<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'subscribe_end',
        'tariff_id'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Company::class, 'user_id', 'id');
    }

    public function chats()
    {
        // Получаем текущего пользователя
        $user = $this;

        // Получаем чаты, в которых участвует текущий пользователь, и последние сообщения
        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender.company', 'receiver.company', 'latestMessage'])
            ->get()
            ->map(function ($chat) use ($user) {
                // Определяем имя компании другого пользователя в чате
                $otherUser = $chat->sender_id === $user->id ? $chat->receiver : $chat->sender;
                return [
                    'chat_id' => $chat->id,
                    'user_name' => $otherUser->name,
                    'company_name' => $otherUser->company?->name,
                    'latest_message' => $chat->latestMessage?->text,
                ];
            });

        return response()->json($chats);
    }
}
