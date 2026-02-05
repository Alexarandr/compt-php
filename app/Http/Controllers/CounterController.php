<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CounterController extends Controller
{
    /**
     * Increment counter by specified amount
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'integer|min:1|max:1000'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input',
                'messages' => $e->errors()
            ], 422);
        }

        $amount = $request->input('amount', 1);
        $counter = Counter::firstOrCreate(
            ['id' => 1],
            ['value' => 0]
        );
        $counter->increment('value', $amount);

        return response()->json([
            'value' => $counter->value,
            'message' => "Counter incremented by $amount"
        ], 200);
    }

    /**
     * Get current counter value
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $counter = Counter::firstOrCreate(
            ['id' => 1],
            ['value' => 0]
        );
        return response()->json([
            'value' => $counter->value
        ], 200);
    }

    /**
     * Reset counter to 0 (admin only)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        // In production, add authorization check
        // $this->authorize('reset-counter');

        $counter = Counter::firstOrCreate(
            ['id' => 1],
            ['value' => 0]
        );
        $counter->update(['value' => 0]);

        return response()->json([
            'value' => 0,
            'message' => 'Counter has been reset'
        ], 200);
    }
}
