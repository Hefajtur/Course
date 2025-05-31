<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{

    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $data = $request->all();
        // dd($data);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'modules' => 'array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents' => 'array',
            'modules.*.contents.*.data' => 'required|string',
            'modules.*.contents.*.type' => 'required|in:text,video,image,link',
        ], [

            'modules.*.title.required' => 'Module title is required.',
            'modules.*.contents.*.data.required' => 'Content data is required.',

        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        foreach ($validated['modules'] ?? [] as $moduleData) {
            $module = $course->modules()->create([
                'title' => $moduleData['title'],
            ]);

            foreach ($moduleData['contents'] ?? [] as $contentData) {
                $module->contents()->create([
                    'data' => $contentData['data'],
                    'type' => $contentData['type'],
                ]);
            }
        }

        return response()->json(['message' => 'Course created successfully']);
    }
}
