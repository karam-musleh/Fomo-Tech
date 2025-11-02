<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Skill;
use App\Models\Resource;
use App\Models\Section;
use App\Models\Track;
use App\Models\Article;
use App\Models\Blog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الطلاب
        $students = User::factory(10)->create(['role' => 'student']);

        // 2. إنشاء مرشدين
        $mentors = Mentor::factory(5)->create();

        // 3. إنشاء الموارد وربطها بمرشدين عشوائيين
        // إنشاء الموارد وربطها بمرشدين عشوائيين
        $resources = Resource::factory(20)->make()->each(function ($resource) use ($mentors) {
            $resource->mentor_id = $mentors->random()->id; // تعيين mentor_id عشوائي لكل Resource
            $resource->save(); // حفظ السجل بعد الإضافة
        });


        // 4. إنشاء سكشنات عادية
        $sections = Section::factory(5)->create();

        // ربط كل سكشن بموارد عشوائية
        $sections->each(function ($section) {
            $resources = Resource::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $section->resources()->attach($resources);
        });

        // 5. سكشن "Tracks"
        $trackSection = Section::factory()->create(['title' => 'Tracks']);

        // إنشاء 15 تراكات داخل سكشن Tracks
        $tracks = Track::factory(15)->create(['section_id' => $trackSection->id]);

        // ربط المرشدين بالتراكات وإنشاء مقالات لكل تراك
        $tracks->each(function ($track) use ($mentors) {
            $assignedMentors = $mentors->random(rand(1, 3))->pluck('id')->toArray();
            $track->mentors()->attach($assignedMentors);

            foreach ($assignedMentors as $mentorId) {
                $articles = Article::factory(rand(1, 2))->create([
                    'mentor_id' => $mentorId,
                ]);

                foreach ($articles as $article) {
                    $track->articles()->attach($article->id);
                }
            }
        });

        // 6. ربط الطلاب بتراكات مفضلة عشوائية
        $students->each(function ($student) use ($tracks) {
            $favoriteTrackIds = $tracks->random(rand(3, 7))->pluck('id')->toArray();
            $student->favoriteTracks()->attach($favoriteTrackIds);
        });

        // 7. إنشاء المهارات وربطها بالمرشدين
        $skills = Skill::factory(10)->create();
        $mentors->each(function ($mentor) use ($skills) {
            $mentor->skills()->attach($skills->random(rand(2, 4))->pluck('id')->toArray());
        });

        // 8. سكشن "Blog"
        $blogSection = Section::factory()->create(['title' => 'Blog']);

        Blog::factory(10)->make()->each(function ($blog) use ($mentors, $blogSection) {
            $blog->mentor_id = $mentors->random()->id;
            $blog->section_id = $blogSection->id;
            $blog->status = Blog::STATUS_DRAFT;
            $blog->save();
        });
    }
}
