<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Condition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConditionBadgeController extends Controller
{
    public function index(): View
    {
        $conditions = Condition::orderBy('id')->get();

        return view('admin.condition-badges.index', compact('conditions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'badges' => 'required|array',
            'badges.*.badge_text' => 'nullable|string|max:30',
            'badges.*.badge_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        foreach (Condition::all() as $condition) {
            $input = $request->input("badges.{$condition->id}");
            if (! is_array($input)) {
                continue;
            }

            // Saved through Eloquent so the cached badge map is flushed.
            $condition->update([
                'badge_active' => ! empty($input['badge_active']),
                'badge_text' => trim((string) ($input['badge_text'] ?? '')) ?: null,
                'badge_color' => strtolower($input['badge_color']),
            ]);
        }

        return redirect()->route('admin.condition-badges.index')->with('success', 'Condition badges updated.');
    }
}
