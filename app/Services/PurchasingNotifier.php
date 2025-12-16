<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\InventoryMovement;
use App\Models\Product;

class PurchasingNotifier
{
    public static function notify(InventoryMovement $movement): void
    {
        // Ensure we have the related data available for the notification payload
        $movement->loadMissing([
            'product',
            'workOrder.technician',
            'workOrder.appointment.company',
            'user',
        ]);

        // Build a material list (fallback to the current movement if none stored on the work order)
        $materials = collect($movement->workOrder?->materials_used ?? [])
            ->map(function ($material) {
                $product = Product::find($material['product_id'] ?? null);

                return [
                    'name'     => $product?->name ?? 'Onbekend product',
                    'quantity' => (int)($material['quantity'] ?? 0),
                ];
            })
            ->values()
            ->all();

        if (! count($materials) && $movement->product) {
            $materials[] = [
                'name'     => $movement->product->name,
                'quantity' => (int)$movement->quantity,
            ];
        }

        $payload = [
            'technician'  => $movement->workOrder?->technician?->name ?? $movement->user?->name,
            'company'     => $movement->workOrder?->appointment?->company?->name,
            'date'        => optional($movement->workOrder?->appointment?->date_planned ?? $movement->created_at)->toDateTimeString(),
            'problem'     => $movement->workOrder?->appointment?->description,
            'solution'    => $movement->workOrder?->solution,
            'materials'   => $materials,
            'workorder_id'=> $movement->related_work_order_id,
        ];

        // alle inkoop + managers
        $users = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('name', ['Inkoop', 'Manager']);
        })->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id'       => $user->id,
                'type'          => 'workorder',
                'work_order_id' => $movement->related_work_order_id,
                'title'         => 'Materiaal gebruikt',
                'message'       => json_encode($payload),
                'link'          => route('maintenance.workorder.form', $movement->related_work_order_id),
                'is_read'       => false,
            ]);
        }
    }
}
