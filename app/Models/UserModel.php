<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable; //implement auth
use App\Models\StudentModel;
use App\Models\LecturerModel;
use App\Models\StaffModel;
use App\Models\AlumniModel;
use App\Models\ExamResultModel;
use App\Models\ExamRegistrationModel;
use App\Models\AdminModel;

class UserModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'identity_number',
        'password',
        'exam_status',
        'certificate_status',
        'phone_number',
        'telegram_chat_id',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Hide the password when serializing the model
        'remember_token', // Hide the remember token when serializing the model
    ];

    protected $casts = [];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isLecturer()
    {
        return $this->role === 'lecturer';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isAlumni()
    {
        return $this->role === 'alumni';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // mutator to hash password
    public function setPasswordAttribute($value)
    {
        if ($value) {
            // Only hash if it's not already hashed
            if (strlen($value) < 60 || !preg_match('/^\$2[ayb]\$.{56}$/', $value)) {
                $this->attributes['password'] = bcrypt($value);
            } else {
                $this->attributes['password'] = $value; // Already hashed, store as is
            }
        }
    }

    /**
     * Get the name of the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }


    public function student()
    {
        return $this->hasOne(StudentModel::class, 'user_id', 'user_id');
    }
    public function staff()
    {
        return $this->hasOne(StaffModel::class, 'user_id', 'user_id');
    }
    public function lecturer()
    {
        return $this->hasOne(LecturerModel::class, 'user_id', 'user_id');
    }
    public function alumni()
    {
        return $this->hasOne(AlumniModel::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(AdminModel::class, 'user_id', 'user_id');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResultModel::class, 'user_id', 'user_id');
    }

    public function examRegistrations()
    {
        return $this->hasMany(ExamRegistrationModel::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Delete related profile based on role
            if ($user->isStudent() && $user->student) {
                $user->student->delete();
            } elseif ($user->isLecturer() && $user->lecturer) {
                $user->lecturer->delete();
            } elseif ($user->isStaff() && $user->staff) {
                $user->staff->delete();
            } elseif ($user->isAlumni() && $user->alumni) {
                $user->alumni->delete();
            } elseif ($user->isAdmin() && $user->admin) { // Add this block for admin
                $user->admin->delete();
            }

            // Delete related exam results and registrations
            $user->examResults()->delete();
            $user->examRegistrations()->delete();
        });
    }
}
