<?php

namespace App\Traits;

use App\Jobs\LogActivityJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            static::logToAudit('created', $model);
        });

        static::updated(function (Model $model) {
            static::logToAudit('updated', $model);
        });

        static::deleted(function (Model $model) {
            static::logToAudit('deleted', $model);
        });
    }

    protected static function logToAudit(string $event, Model $model)
    {
        // Don't log if running in console (unless needed) or if disabled
        // if (app()->runningInConsole()) return;

        $oldValues = null;
        $newValues = null;

        if ($event === 'updated') {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
        } elseif ($event === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        // Hide huge text fields or sensitive data if needed (optional)
        // $newValues = collect($newValues)->except(['password'])->toArray();

        $data = [
            'user_id' => Auth::id(), // Might be null for system actions
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        // Dispatch job to queue
        LogActivityJob::dispatch($data);
    }
}
