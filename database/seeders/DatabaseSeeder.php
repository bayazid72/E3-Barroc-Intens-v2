<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Role, User, Company, ProductCategory, Product,
    Contract, ContractLine, Invoice, InvoiceLine,
    Appointment, WorkOrder, Note, InventoryMovement
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ===========================================
         * 1. Rollen
         * ===========================================
        */
        $roles = [
            'Finance',
            'Sales',
            'Inkoop',
            'Manager',
            'MaintenanceManager',
            'Planner',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        /* ===========================================
         * 2. Users
         * ===========================================
        */
        $users = [
            'Manager'            => ['Admin Manager', 'admin@example.com'],
            'Finance'            => ['Finance User', 'finance@example.com'],
            'Sales'              => ['Sales User', 'sales@example.com'],
            'Inkoop'             => ['Inkoop User', 'inkoop@example.com'],
            'MaintenanceManager' => ['Maintenance Manager', 'maintmanager@example.com'],
            'Planner'            => ['Planner User', 'planner@example.com'],
        ];

        foreach ($users as $roleName => $info) {
            [$name, $email] = $info;
            $role = Role::where('name', $roleName)->first();

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('123'),
                    'role_id' => $role->id,
                ]
            );
        }

        $firstUser = User::first();


        /* ===========================================
         * 3. Company
         * ===========================================
        */
        $company = Company::firstOrCreate(
            ['name' => 'Test BV'],
            [
                'phone' => '0201234567',
                'street' => 'Hoofdstraat',
                'house_number' => '10A',
                'zip' => '1000AA',
                'city' => 'Amsterdam',
                'country_code' => 'NL',
                'bkr_checked' => false,
                'contact_id' => $firstUser->id,
            ]
        );


        /* ===========================================
         * 4. Product categories
         * ===========================================
        */
        $categoryCoffee = ProductCategory::firstOrCreate([
            'name' => 'Koffie',
        ], [
            'is_employee_only' => false,
        ]);

        $categoryParts = ProductCategory::firstOrCreate([
            'name' => 'Onderdelen',
        ], [
            'is_employee_only' => true,
        ]);


        /* ===========================================
         * 5. Products
         * ===========================================
        */
        $product1 = Product::firstOrCreate([
            'name' => 'Koffiebonen 1kg',
        ], [
            'description' => 'Premium koffiebonen',
            'price' => 14.95,
            'product_category_id' => $categoryCoffee->id,
        ]);

        $product2 = Product::firstOrCreate([
            'name' => 'Pomp Onderdeel',
        ], [
            'description' => 'Onderdeel voor onderhoud',
            'price' => 49.99,
            'product_category_id' => $categoryParts->id,
        ]);


        /* ===========================================
         * 6. Contract + Contract Lines
         * ===========================================
        */
        $contract = Contract::firstOrCreate([
            'company_id' => $company->id,
        ], [
            'starts_at' => now()->subMonths(1),
            'invoice_type' => 'monthly',
            'created_by' => $firstUser->id,
        ]);

        ContractLine::firstOrCreate([
            'contract_id' => $contract->id,
            'product_id' => $product1->id
        ], [
            'amount' => 1,
            'price_snapshot' => $product1->price,
            'beans_per_month' => 1000,
        ]);


        /* ===========================================
         * 7. Invoice + lines
         * ===========================================
        */
        $invoice = Invoice::firstOrCreate([
            'company_id' => $company->id,
        ], [
            'contract_id' => $contract->id,
            'invoice_date' => now(),
            'total_amount' => $product1->price,
            'type' => 'invoice',
            'is_sent' => false,
        ]);

        InvoiceLine::firstOrCreate([
            'invoice_id' => $invoice->id,
            'product_id' => $product1->id,
        ], [
            'amount' => 1,
            'price_snapshot' => $product1->price,
        ]);


        /* ===========================================
         * 8. Appointment
         * ===========================================
        */
        $technician = User::whereHas('role', fn($q) => $q->where('name', 'MaintenanceManager'))->first();

        $appointment = Appointment::create([
    'company_id' => $company->id,
    'technician_id' => $technician?->id,
    'type' => 'routine',
    'date_planned' => now()->addDays(1),
    'date_added' => now(),
    'status' => 'planned',
]);




        /* ===========================================
         * 9. WorkOrder
         * ===========================================
        */
        WorkOrder::firstOrCreate([
            'appointment_id' => $appointment->id,
        ], [
            'technician_id' => $technician?->id,
            'notes' => 'Routine onderhoud uitgevoerd.',
            'solution' => 'Alles werkt naar behoren.',
            'materials_used' => json_encode([
                ['product' => 'Pomp Onderdeel', 'qty' => 1]
            ])
        ]);


        /* ===========================================
         * 10. Note
         * ===========================================
        */
        Note::firstOrCreate([
            'company_id' => $company->id,
        ], [
            'note' => 'Eerste notitie bij dit bedrijf.',
            'date' => now(),
            'author_id' => $firstUser->id,
        ]);


        /* ===========================================
         * 11. Inventory Movement
         * ===========================================
        */
        InventoryMovement::firstOrCreate([
            'product_id' => $product1->id,
            'type' => 'purchase',
        ], [
            'quantity' => 10,
            'user_id' => $firstUser->id,
        ]);
    }
}
