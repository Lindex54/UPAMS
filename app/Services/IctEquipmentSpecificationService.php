<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetType;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class IctEquipmentSpecificationService
{
    /** @return list<array<string, mixed>> */
    public function fieldsForType(?AssetType $type): array
    {
        $profile = config('ict_equipment.type_profiles.'.$type?->name);

        return is_string($profile) ? config('ict_equipment.profiles.'.$profile, []) : [];
    }

    /** @param iterable<int, AssetType> $types
     * @return array<int, list<array<string, mixed>>>
     */
    public function schemasForTypes(iterable $types): array
    {
        $schemas = [];

        foreach ($types as $type) {
            $schemas[$type->id] = $this->fieldsForType($type);
        }

        return $schemas;
    }

    /** @return array<string, mixed> */
    public function validationRules(?AssetType $type): array
    {
        $fields = $this->fieldsForType($type);
        $keys = array_column($fields, 'key');
        $rules = [
            'specifications' => $keys === [] ? ['nullable', 'array', 'max:0'] : ['nullable', 'array:'.implode(',', $keys)],
        ];

        foreach ($fields as $field) {
            $fieldRules = [($field['required'] ?? false) ? 'required' : 'nullable'];
            if (($field['type'] ?? 'text') === 'number') {
                $fieldRules = [...$fieldRules, 'numeric', 'min:0'];
            } elseif (($field['type'] ?? 'text') === 'date') {
                $fieldRules[] = 'date';
            } elseif (($field['type'] ?? 'text') === 'select') {
                $fieldRules[] = Rule::in($field['options'] ?? []);
            } else {
                $fieldRules = [...$fieldRules, 'string', 'max:255'];
            }

            $rules['specifications.'.$field['key']] = $fieldRules;
        }

        return $rules;
    }

    /** @param array<string, mixed>|null $values
     * @return array<string, mixed>
     */
    public function sanitize(?AssetType $type, ?array $values): array
    {
        $values ??= [];
        $allowed = collect($this->fieldsForType($type))
            ->filter(fn (array $field): bool => $this->isFieldVisible($field, $values))
            ->pluck('key')
            ->all();

        return array_filter(
            Arr::only($values, $allowed),
            fn (mixed $value): bool => $value !== null && $value !== '',
        );
    }

    /** @return array<string, mixed> */
    public function valuesFor(Asset $asset): array
    {
        $values = $asset->specifications ?? [];
        $legacyValues = ['processor' => $asset->processor, 'ram_size' => $asset->ram, 'storage_capacity' => $asset->storage];

        foreach ($legacyValues as $key => $value) {
            if (! array_key_exists($key, $values) && filled($value)) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /** @return list<array{label: string, value: string}> */
    public function displayValues(Asset $asset): array
    {
        $values = $this->valuesFor($asset);

        return collect($this->fieldsForType($asset->type))
            ->filter(fn (array $field): bool => $this->isFieldVisible($field, $values) && filled($values[$field['key']] ?? null))
            ->map(fn (array $field): array => ['label' => $field['label'], 'value' => (string) $values[$field['key']]])
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $field
     * @param  array<string, mixed>  $values
     */
    private function isFieldVisible(array $field, array $values): bool
    {
        if (! isset($field['show_when'])) {
            return true;
        }

        return in_array($values[$field['show_when']['key']] ?? null, $field['show_when']['values'], true);
    }
}
