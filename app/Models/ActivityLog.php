<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model.
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get action badge color.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'create', 'stock_in' => 'green',
            'update' => 'blue',
            'delete', 'stock_out' => 'red',
            'login' => 'indigo',
            'logout' => 'gray',
            'checkout' => 'purple',
            'export' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get module icon.
     */
    public function getModuleIconAttribute(): string
    {
        return match($this->module) {
            'inventory' => 'fas fa-boxes',
            'pos' => 'fas fa-cash-register',
            'finance' => 'fas fa-dollar-sign',
            'users' => 'fas fa-users',
            'auth' => 'fas fa-lock',
            'batch' => 'fas fa-layer-group',
            default => 'fas fa-circle',
        };
    }
}
