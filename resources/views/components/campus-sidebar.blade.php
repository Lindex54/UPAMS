@php
    $campusNavigationGroups = [
        ['label' => 'Campus Overview', 'items' => [
            ['label' => 'Dashboard', 'href' => route('campus.dashboard'), 'pattern' => 'campus.dashboard', 'route' => true],
            ['label' => 'Map/Location View', 'href' => route('campus.map'), 'pattern' => 'campus.map', 'route' => true],
        ]],
        ['label' => 'Property Records', 'items' => [
            ['label' => 'Asset Registry', 'href' => route('asset-management.assets.index'), 'pattern' => 'assets*'],
            ['label' => 'Land Management', 'href' => route('asset-management.land.index'), 'pattern' => 'land*'],
            ['label' => 'Buildings & Spaces', 'href' => route('asset-management.buildings.index'), 'pattern' => 'buildings*'],
            ['label' => 'Commercial Property', 'href' => route('asset-management.commercial-property.index'), 'pattern' => 'commercial-property*'],
            ['label' => 'Agricultural Property', 'href' => route('asset-management.agricultural-property.index'), 'pattern' => 'agricultural-property*'],
        ]],
        ['label' => 'Campus Operations', 'items' => [
            ['label' => 'Agreements & Allocations', 'href' => route('operations.agreements.index'), 'pattern' => 'agreements*'],
            ['label' => 'Tenants/Beneficiaries', 'href' => route('operations.beneficiaries.index'), 'pattern' => 'beneficiaries*'],
            ['label' => 'Inspections', 'href' => route('operations.inspections.index'), 'pattern' => 'inspections*'],
            ['label' => 'Maintenance', 'href' => route('operations.maintenance.index'), 'pattern' => 'maintenance*'],
            ['label' => 'Documents', 'href' => route('operations.documents.index'), 'pattern' => 'documents*'],
        ]],
        ['label' => 'Updates & Insight', 'items' => [
            ['label' => 'Notifications', 'href' => route('notifications.index'), 'pattern' => 'notifications*'],
            ['label' => 'Reports', 'href' => route('reports.index'), 'pattern' => 'reports*'],
        ]],
    ];
@endphp

<x-sidebar
    :navigation-groups="$campusNavigationGroups"
    aria-label="Campus Property Officer navigation"
    sidebar-id="campus-property-officer-sidebar"
/>
