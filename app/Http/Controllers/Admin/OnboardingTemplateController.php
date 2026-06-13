<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingTemplate;
use Illuminate\Http\Request;

class OnboardingTemplateController extends Controller
{
    public function index()
    {
        $templates = OnboardingTemplate::latest()->paginate(10);

        return view('admin.onboarding.templates.index', [
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        return view('admin.onboarding.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'default_expiry_days' => ['required', 'integer', 'min:1', 'max:90'],
            'required_fields_text' => ['nullable', 'string'],
            'required_documents_text' => ['nullable', 'string'],
            'review_checklist_text' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template = OnboardingTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'default_expiry_days' => $validated['default_expiry_days'],
            'required_fields' => $this->linesToArray($validated['required_fields_text'] ?? ''),
            'required_documents' => $this->linesToArray($validated['required_documents_text'] ?? ''),
            'review_checklist' => $this->linesToArray($validated['review_checklist_text'] ?? ''),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.onboarding.templates.show', $template)
            ->with('success', 'Onboarding template created.');
    }

    public function show(OnboardingTemplate $template)
    {
        return view('admin.onboarding.templates.show', [
            'template' => $template,
        ]);
    }

    private function linesToArray(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}