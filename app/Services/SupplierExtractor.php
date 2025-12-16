<?php

namespace App\Services;

class SupplierExtractor
{
    public static function fromDescription(?string $description): ?string
    {
        if (!$description) {
            return null;
        }

        if (preg_match('/supplier:\s*(.+)/i', $description, $m)) {
            return trim($m[1]);
        }

        return null;
    }
}
