<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Collects every record that carries GPS coordinates into one map-ready feature list.
 *
 * To put a new kind of record on the GIS map, add a layer to LAYERS and a matching
 * collector method that returns features in the shape documented on features().
 */
class GisFeatureService
{
    /**
     * Map layers, in the order they appear in the legend.
     *
     * @var array<string, array{label: string, color: string}>
     */
    public const LAYERS = [
        'campuses' => ['label' => 'Campuses', 'color' => '#1e73be'],
        'assets' => ['label' => 'Assets', 'color' => '#d97706'],
    ];

    /**
     * @return Collection<int, array{id: string, layer: string, title: string, subtitle: ?string, badge: ?string, status: ?string, campus: ?string, latitude: float, longitude: float, url: ?string, details: list<array{0: string, 1: string}>}>
     */
    public function features(User $viewer): Collection
    {
        return $this->campusFeatures()
            ->concat($this->assetFeatures($viewer))
            ->values();
    }

    /**
     * @return array<string, array{label: string, color: string, count: int}>
     */
    public function layers(Collection $features): array
    {
        $counts = $features->countBy('layer');

        return collect(self::LAYERS)
            ->map(fn (array $layer, string $key): array => $layer + ['count' => (int) ($counts[$key] ?? 0)])
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function campusFeatures(): Collection
    {
        return Campus::query()
            ->with('district:id,name')
            ->withCount(['assets', 'users'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get()
            ->map(fn (Campus $campus): array => [
                'id' => "campus-{$campus->id}",
                'layer' => 'campuses',
                'title' => $campus->name,
                'subtitle' => $campus->faculty,
                'badge' => $campus->code,
                'status' => $campus->status,
                'campus' => $campus->name,
                'latitude' => (float) $campus->latitude,
                'longitude' => (float) $campus->longitude,
                'url' => route('campuses.show', $campus),
                'details' => $this->details([
                    'District' => $campus->district?->name,
                    'Location' => $campus->location,
                    'Assets' => number_format($campus->assets_count),
                    'Users' => number_format($campus->users_count),
                ]),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function assetFeatures(User $viewer): Collection
    {
        $canOpenIctRecords = in_array($viewer->role?->name, ['IT Technician', 'System Administrator'], true);

        return Asset::query()
            ->with(['campus:id,name', 'type:id,name', 'category:id,name'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get()
            ->map(fn (Asset $asset): array => [
                'id' => "asset-{$asset->id}",
                'layer' => 'assets',
                'title' => $asset->name,
                'subtitle' => $asset->type?->name ?? $asset->category?->name,
                'badge' => $asset->reference,
                'status' => $asset->operational_status ?? $asset->status,
                'campus' => $asset->campus?->name,
                'latitude' => (float) $asset->latitude,
                'longitude' => (float) $asset->longitude,
                'url' => $asset->is_ict && $canOpenIctRecords ? route('technician.equipment.show', $asset) : null,
                'details' => $this->details([
                    'Campus' => $asset->campus?->name,
                    'Location' => collect([$asset->building, $asset->room])->filter()->implode(' · '),
                    'Condition' => $asset->condition,
                    'Custodian' => $asset->custodian,
                ]),
            ]);
    }

    /**
     * Keep only the summary rows that have a value.
     *
     * @param  array<string, mixed>  $rows
     * @return list<array{0: string, 1: string}>
     */
    private function details(array $rows): array
    {
        return collect($rows)
            ->filter(fn (mixed $value): bool => filled($value))
            ->map(fn (mixed $value, string $label): array => [$label, (string) $value])
            ->values()
            ->all();
    }
}
