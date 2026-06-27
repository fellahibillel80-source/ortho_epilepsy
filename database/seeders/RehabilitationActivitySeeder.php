<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RehabilitationActivity;

class RehabilitationActivitySeeder extends Seeder
{
    public function run(): void
    {
        RehabilitationActivity::firstOrCreate(
            ['slug' => 'phoneme-pronunciation'],
            [
                'name_ar' => 'تمرين مخارج الحروف الأساسي',
                'name_en' => 'Basic Phoneme Pronunciation',
                'description_ar' => 'تمرين مخصص لتحسين النطق ومخارج الحروف وتأهيل عضلات النطق.',
                'description_en' => 'A basic exercise designed to improve articulation, sound production, and oral motor skills.',
                'category' => 'speech',
            ]
        );

        RehabilitationActivity::firstOrCreate(
            ['slug' => 'pattern-recognition'],
            [
                'name_ar' => 'لعبة التركيز وتحديد الأنماط',
                'name_en' => 'Pattern Recognition Game',
                'description_ar' => 'نشاط ذهني تفاعلي لتحسين مرونة التفكير وسرعة المعالجة البصرية.',
                'description_en' => 'An interactive cognitive task to enhance cognitive flexibility and visual processing speed.',
                'category' => 'cognitive',
            ]
        );

        RehabilitationActivity::firstOrCreate(
            ['slug' => 'muscle-relaxation'],
            [
                'name_ar' => 'تمرين الاسترخاء وتصفية الذهن',
                'name_en' => 'Mindful Muscle Relaxation',
                'description_ar' => 'تمرين حركي خفيف لتنظيم التنفس والتخفيف من التشنجات العضلية المصاحبة.',
                'description_en' => 'A simple motor-relaxation guide to regulate breathing and ease motor tension.',
                'category' => 'motor',
            ]
        );
    }
}
