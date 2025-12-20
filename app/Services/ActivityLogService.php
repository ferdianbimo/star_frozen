<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param string $action The action performed (create, update, delete, etc.)
     * @param string $module The module where the action occurred
     * @param string $description Human-readable description
     * @param mixed $model The model being affected (optional)
     * @param array|null $oldValues Previous values for updates
     * @param array|null $newValues New values for creates/updates
     * @param array|null $metadata Additional metadata
     * @return ActivityLog
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a create action.
     */
    public static function logCreate(string $module, string $description, $model = null, ?array $newValues = null): ActivityLog
    {
        return self::log('create', $module, $description, $model, null, $newValues);
    }

    /**
     * Log an update action.
     */
    public static function logUpdate(string $module, string $description, $model = null, ?array $oldValues = null, ?array $newValues = null): ActivityLog
    {
        return self::log('update', $module, $description, $model, $oldValues, $newValues);
    }

    /**
     * Log a delete action.
     */
    public static function logDelete(string $module, string $description, $model = null, ?array $oldValues = null): ActivityLog
    {
        return self::log('delete', $module, $description, $model, $oldValues);
    }

    /**
     * Log a stock in action.
     */
    public static function logStockIn(string $description, $model = null, ?array $newValues = null): ActivityLog
    {
        return self::log('stock_in', 'inventory', $description, $model, null, $newValues);
    }

    /**
     * Log a stock out action.
     */
    public static function logStockOut(string $description, $model = null, ?array $metadata = null): ActivityLog
    {
        return self::log('stock_out', 'inventory', $description, $model, null, null, $metadata);
    }

    /**
     * Log a checkout/sale action.
     */
    public static function logCheckout(string $description, $transaction = null, ?array $metadata = null): ActivityLog
    {
        return self::log('checkout', 'pos', $description, $transaction, null, null, $metadata);
    }

    /**
     * Log a batch creation.
     */
    public static function logBatchCreate(string $description, $batch = null, ?array $newValues = null): ActivityLog
    {
        return self::log('create', 'batch', $description, $batch, null, $newValues);
    }

    /**
     * Log a login action.
     */
    public static function logLogin(?int $userId = null): ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} logged in" : "User logged in";
        
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => 'login',
            'module' => 'auth',
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a logout action.
     */
    public static function logLogout(): ActivityLog
    {
        $user = Auth::user();
        $description = $user ? "User {$user->name} logged out" : "User logged out";
        
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'module' => 'auth',
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log an export action.
     */
    public static function logExport(string $module, string $description, ?array $metadata = null): ActivityLog
    {
        return self::log('export', $module, $description, null, null, null, $metadata);
    }
}
