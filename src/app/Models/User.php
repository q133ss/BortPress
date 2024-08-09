<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

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

    public function isAdmin(): bool
    {
        if($this->role->slug == 'admin'){
            return true;
        }
        return false;
    }

    public function ads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ad::class, 'user_id', 'id')->where('is_archive', 0);
    }

    public function archive(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ad::class, 'user_id', 'id')->where('is_archive', 1);
    }

    private function isJoined($query, $table): bool
    {
        $joins = collect($query->getQuery()->joins);
        return $joins->pluck('table')->contains($table);
    }

    public function subscribe_status()
    {
        if ($this->subscribe_end === null) {
            return false;
        }

        $subscribeEndDate = Carbon::parse($this->subscribe_end);

        return now()->lessThan($subscribeEndDate);
    }

    public function scopeWithSort($query, Request $request)
    {
        // Получаем поле для сортировки из запроса
        $sortBy = $request->query('sort_by', 'name'); // 'name' - значение по умолчанию

        // Определяем направление сортировки, по умолчанию 'asc' (по возрастанию)
        $sortDirection = $request->query('sort_direction', 'asc');

        // Проверяем, чтобы поле для сортировки было допустимым
        if (in_array($sortBy, ['name', 'email', 'created_at'])) {
            // Применяем сортировку
            return $query->orderBy($sortBy, $sortDirection);
        }

        // Если поле недопустимо, можно вернуть запрос без сортировки или сортировать по умолчанию
        return $query->orderBy('name', 'asc');
    }

    public function activation_date()
    {
        // Дата активации юзера
        $date = Payment::where('user_id', $this->id)
            ->where('status', 'done')
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        return Carbon::parse($date)->format('d-m-Y');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
