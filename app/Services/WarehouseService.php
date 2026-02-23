<?php

namespace App\Services;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class WarehouseService
{
    /**
     * Get all warehouses.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWarehouses()
    {
        return Warehouse::with('manager')->latest()->get();
    }

    /**
     * Create a new warehouse and its manager.
     *
     * @param array $data
     * @return Warehouse
     * @throws Exception
     */
    public function createWarehouse(array $data): Warehouse
    {
        // Trim string inputs
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        return DB::transaction(function () use ($data) {
            // Step 1: Create the User (Manager)
            $manager = User::create([
                'name' => $data['manager_name'],
                'email' => $data['manager_email'],
                'mobile' => $data['manager_phone'], // Map manager_phone to mobile
                'password' => Hash::make($data['manager_password']),
                'is_active' => true,
            ]);

            // Step 2: Assign the warehouse_manager role
            // Ensure the role exists or handle accordingly. Assuming 'Warehouse Manager' exists from RoleSeeder.
            $manager->assignRole('Warehouse Manager');

            // Step 3: Create the Warehouse
            $warehouse = Warehouse::create([
                'name' => $data['name'],
                'warehouse_code' => $data['warehouse_code'],
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip_code' => $data['zip_code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'timezone' => $data['timezone'] ?? 'UTC',
                'opening_time' => $data['opening_time'] ?? null,
                'closing_time' => $data['closing_time'] ?? null,
                'weekly_off_day' => $data['weekly_off_day'] ?? null,
                'manager_id' => $manager->id,
                'status' => true,
            ]);

            return $warehouse->load('manager');
        });
    }

    /**
     * Update warehouse details.
     *
     * @param Warehouse $warehouse
     * @param array $data
     * @return Warehouse
     */
    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        // Trim string inputs
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        $warehouse->update($data);
        return $warehouse;
    }

    /**
     * Toggle the status of a warehouse.
     *
     * @param Warehouse $warehouse
     * @return bool New status
     */
    public function toggleStatus(Warehouse $warehouse): bool
    {
        $warehouse->status = ! $warehouse->status;
        $warehouse->save();
        return $warehouse->status;
    }
}
