<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    //
    // protected static function bootHasSlug()  {
    //     static::creating(function ($model) {
    //         if (empty($model->slug) && isset($model->title)) {
    //             $slug = Str::slug($model->title);
    //             $count = static::where('slug', 'like', "{$slug}%")->count();
    //             $model->slug = $count ? "{$slug}-" . ($count + 1) : $slug;
    //         }
    //     });
    // }
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            // إذا ما في slug مدخَل، انشئ واحد جديد
            if (empty($model->slug) && isset($model->title)) {
                $baseSlug = Str::slug($model->title);

                $slug = $baseSlug;
                $count = 1;

                // كرّر لحد ما تلاقي slug غير مستخدم
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });

        static::updating(function ($model) {
            // في حال تغيّر العنوان، حدّث الـ slug
            if ($model->isDirty('title')) {
                $baseSlug = Str::slug($model->title);

                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)
                    ->where('id', '!=', $model->id)
                    ->exists()
                ) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });
    }
}
