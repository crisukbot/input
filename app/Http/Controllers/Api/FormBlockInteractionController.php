<?php

namespace App\Http\Controllers\Api;

use App\Models\FormBlock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\FormBlockInteraction;
use App\Enums\FormBlockInteractionType;

class FormBlockInteractionController extends Controller
{
    public function create(Request $request, FormBlock $block)
    {
        $request->validate([
            'type' => ['required', Rule::in(array_map(
                fn ($i) => $i->value,
                FormBlockInteractionType::cases()
            ))],
        ]);

        $interaction = new FormBlockInteraction([
            'type' => $request->input('type'),
        ]);

        $block->interactions()->save($interaction);

        return response()->json($interaction->fresh(), 201);
    }

    public function update(Request $request, FormBlockInteraction $interaction)
    {
        switch ($interaction->type) {
            case FormBlockInteractionType::button:
                $request->validate([
                    'label' => 'min:1',
                ]);
                break;
        }

        $interaction->fill($request->only(['label', 'reply', 'uuid']));
        $interaction->save();

        return response()->json($interaction, 200);
    }

    public function delete(Request $request, FormBlockInteraction $interaction)
    {
        $interaction->delete();

        return response()->json('', 200);
    }
}
