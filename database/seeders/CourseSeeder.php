<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['name' => 'Web Development', 'code' => 'WEB', 'duration' => '6 Months', 'fee' => 18000],
            ['name' => 'Graphic Design', 'code' => 'GD', 'duration' => '45 Days', 'fee' => 7000],
            ['name' => 'Digital Marketing', 'code' => 'DM', 'duration' => '1 Month', 'fee' => 9000],
            ['name' => 'Full Stack Development', 'code' => 'FSD', 'duration' => '1 Year', 'fee' => 42000],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course + ['status' => true]);
        }
    }
}
