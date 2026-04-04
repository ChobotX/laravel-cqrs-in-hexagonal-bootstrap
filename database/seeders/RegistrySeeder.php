<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EntryModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class RegistrySeeder extends Seeder
{
    private const string DEF_MANUFACTURER_ID = '00000000-0000-0000-0000-000000000100';

    private const string DEF_CAR_ID = '00000000-0000-0000-0000-000000000101';

    private const string DEF_PRIORITY_ID = '00000000-0000-0000-0000-000000000102';

    public function run(): void
    {
        $this->seedManufacturers();
        $this->seedCars();
        $this->seedPriorities();
    }

    private function seedManufacturers(): void
    {
        DefinitionModel::create([
            'id' => self::DEF_MANUFACTURER_ID,
            'namespace' => 'enumerations',
            'slug' => 'car_manufacturer',
            'name' => 'Car Manufacturer',
        ]);

        $versionId = Str::uuid()->toString();
        $schema = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => [
                'country' => [
                    'type' => 'string',
                    'x-field-label' => 'Country',
                ],
                'founded_year' => [
                    'type' => 'integer',
                    'x-field-label' => 'Founded Year',
                    'minimum' => 1800,
                    'maximum' => 2030,
                ],
            ],
            'required' => ['country'],
        ];

        DefinitionVersionModel::create([
            'id' => $versionId,
            'definition_id' => self::DEF_MANUFACTURER_ID,
            'version' => 1,
            'body' => $schema,
            'status' => 'active',
        ]);

        $manufacturers = [
            ['title' => 'Toyota', 'data' => ['country' => 'Japan', 'founded_year' => 1937]],
            ['title' => 'BMW', 'data' => ['country' => 'Germany', 'founded_year' => 1916]],
            ['title' => 'Ford', 'data' => ['country' => 'United States', 'founded_year' => 1903]],
            ['title' => 'Ferrari', 'data' => ['country' => 'Italy', 'founded_year' => 1947]],
            ['title' => 'Hyundai', 'data' => ['country' => 'South Korea', 'founded_year' => 1967]],
        ];

        foreach ($manufacturers as $manufacturer) {
            EntryModel::create([
                'id' => Str::uuid()->toString(),
                'definition_id' => self::DEF_MANUFACTURER_ID,
                'definition_version' => 1,
                'namespace' => 'enumerations',
                'title' => $manufacturer['title'],
                'data' => $manufacturer['data'],
            ]);
        }
    }

    private function seedCars(): void
    {
        DefinitionModel::create([
            'id' => self::DEF_CAR_ID,
            'namespace' => 'enumerations',
            'slug' => 'car',
            'name' => 'Car',
        ]);

        $manufacturerEntries = EntryModel::where('definition_id', self::DEF_MANUFACTURER_ID)->get();
        /** @var array<string, string> $manufacturerIds title => id */
        $manufacturerIds = [];

        foreach ($manufacturerEntries as $entry) {
            $manufacturerIds[$entry->title] = $entry->id;
        }

        $versionId = Str::uuid()->toString();
        $schema = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => [
                'model' => [
                    'type' => 'string',
                    'x-field-label' => 'Model',
                ],
                'manufacturer' => [
                    'type' => 'string',
                    'format' => 'uuid',
                    'x-field-label' => 'Manufacturer',
                    'x-reference' => ['namespace' => 'enumerations', 'slug' => 'car_manufacturer'],
                ],
                'year' => [
                    'type' => 'integer',
                    'x-field-label' => 'Year',
                    'minimum' => 1900,
                    'maximum' => 2030,
                ],
                'electric' => [
                    'type' => 'boolean',
                    'x-field-label' => 'Electric',
                ],
            ],
            'required' => ['model', 'manufacturer', 'year'],
        ];

        DefinitionVersionModel::create([
            'id' => $versionId,
            'definition_id' => self::DEF_CAR_ID,
            'version' => 1,
            'body' => $schema,
            'status' => 'active',
        ]);

        $cars = [
            ['title' => 'Corolla', 'data' => ['model' => 'Corolla', 'manufacturer' => $manufacturerIds['Toyota'], 'year' => 2024, 'electric' => false]],
            ['title' => 'Camry', 'data' => ['model' => 'Camry', 'manufacturer' => $manufacturerIds['Toyota'], 'year' => 2024, 'electric' => false]],
            ['title' => '3 Series', 'data' => ['model' => '3 Series', 'manufacturer' => $manufacturerIds['BMW'], 'year' => 2024, 'electric' => false]],
            ['title' => 'iX', 'data' => ['model' => 'iX', 'manufacturer' => $manufacturerIds['BMW'], 'year' => 2024, 'electric' => true]],
            ['title' => 'Mustang Mach-E', 'data' => ['model' => 'Mustang Mach-E', 'manufacturer' => $manufacturerIds['Ford'], 'year' => 2024, 'electric' => true]],
            ['title' => 'F-150', 'data' => ['model' => 'F-150', 'manufacturer' => $manufacturerIds['Ford'], 'year' => 2024, 'electric' => false]],
            ['title' => 'SF90 Stradale', 'data' => ['model' => 'SF90 Stradale', 'manufacturer' => $manufacturerIds['Ferrari'], 'year' => 2023, 'electric' => false]],
            ['title' => 'Ioniq 5', 'data' => ['model' => 'Ioniq 5', 'manufacturer' => $manufacturerIds['Hyundai'], 'year' => 2024, 'electric' => true]],
        ];

        foreach ($cars as $car) {
            EntryModel::create([
                'id' => Str::uuid()->toString(),
                'definition_id' => self::DEF_CAR_ID,
                'definition_version' => 1,
                'namespace' => 'enumerations',
                'title' => $car['title'],
                'data' => $car['data'],
            ]);
        }
    }

    private function seedPriorities(): void
    {
        DefinitionModel::create([
            'id' => self::DEF_PRIORITY_ID,
            'namespace' => 'enumerations',
            'slug' => 'priority',
            'name' => 'Priority Level',
        ]);

        $versionId = Str::uuid()->toString();
        $schema = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => [
                'color' => [
                    'type' => 'string',
                    'x-field-label' => 'Color',
                ],
                'order' => [
                    'type' => 'integer',
                    'x-field-label' => 'Sort Order',
                    'minimum' => 0,
                ],
            ],
            'required' => ['color', 'order'],
        ];

        DefinitionVersionModel::create([
            'id' => $versionId,
            'definition_id' => self::DEF_PRIORITY_ID,
            'version' => 1,
            'body' => $schema,
            'status' => 'active',
        ]);

        $priorities = [
            ['title' => 'Critical', 'data' => ['color' => '#DC2626', 'order' => 1]],
            ['title' => 'High', 'data' => ['color' => '#EA580C', 'order' => 2]],
            ['title' => 'Medium', 'data' => ['color' => '#CA8A04', 'order' => 3]],
            ['title' => 'Low', 'data' => ['color' => '#16A34A', 'order' => 4]],
            ['title' => 'None', 'data' => ['color' => '#6B7280', 'order' => 5]],
        ];

        foreach ($priorities as $priority) {
            EntryModel::create([
                'id' => Str::uuid()->toString(),
                'definition_id' => self::DEF_PRIORITY_ID,
                'definition_version' => 1,
                'namespace' => 'enumerations',
                'title' => $priority['title'],
                'data' => $priority['data'],
            ]);
        }
    }
}
