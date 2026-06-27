<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CognitiveTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cognitive_tests')->insertOrIgnore([
            [
                'slug' => 'stroop',
                'name_ar' => 'اختبار ستروب',
                'name_en' => 'Stroop Test',
                'description_ar' => 'اقرأ لون الحبر وليس الكلمة المكتوبة بأسرع وقت ممكن.',
                'description_en' => 'Identify the ink color of the word rather than the written text as fast as possible.',
                'executive_function' => 'inhibitory_control',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'digit-span',
                'name_ar' => 'اختبار مدى الأرقام',
                'name_en' => 'Digit Span',
                'description_ar' => 'تذكر الأرقام التي تظهر تباعاً وأعد إدخالها بالترتيب المطلوب.',
                'description_en' => 'Recall the sequence of digits presented and enter them in the specified order.',
                'executive_function' => 'working_memory',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'go-no-go',
                'name_ar' => 'اختبار Go/No-Go',
                'name_en' => 'Go/No-Go Test',
                'description_ar' => 'اضغط بسرعة عند ظهور المنبه الصحيح، وتوقف تماماً عند ظهور المنبه الآخر.',
                'description_en' => 'Tap quickly when the target stimulus appears, and refrain from tapping for the distractor.',
                'executive_function' => 'response_inhibition',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'wcst',
                'name_ar' => 'اختبار ويسكونسن لتصنيف البطاقات',
                'name_en' => 'Wisconsin Card Sorting Test (WCST)',
                'description_ar' => 'رتب البطاقات حسب القاعدة الصحيحة، وإذا تغيرت القاعدة حاول اكتشافها.',
                'description_en' => 'Sort cards according to the correct rule. When the rule changes, try to discover the new one.',
                'executive_function' => 'cognitive_flexibility',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'verbal-fluency',
                'name_ar' => 'اختبار الطلاقة اللفظية',
                'name_en' => 'Verbal Fluency Test',
                'description_ar' => 'اذكر أكبر عدد ممكن من الكلمات التي تبدأ بحرف معين خلال دقيقة واحدة.',
                'description_en' => 'Produce as many words starting with a specific letter as possible in one minute.',
                'executive_function' => 'cognitive_flexibility_retrieval',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'tmt',
                'name_ar' => 'اختبار توصيل المسارات (TMT)',
                'name_en' => 'Trail Making Test (TMT)',
                'description_ar' => 'اربط الأرقام بالترتيب التصاعدي، ثم الأرقام والحروف بالتناوب.',
                'description_en' => 'Connect numbers in ascending order (Part A) or alternate between numbers and letters (Part B).',
                'executive_function' => 'cognitive_switching_attention',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
