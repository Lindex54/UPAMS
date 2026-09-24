@if (auth()->user()->role?->name === 'System Administrator')
    <x-sidebar />
@else
@php
    $technicianNavigation = [
        ['label' => 'ICT Overview', 'items' => [
            ['label' => 'Dashboard', 'href' => route('technician.dashboard'), 'pattern' => 'technician.dashboard', 'route' => true],
            ['label' => 'Computer Labs', 'href' => route('technician.labs.index'), 'pattern' => 'technician.labs.*', 'route' => true],
            ['label' => 'ICT Equipment', 'href' => route('technician.equipment.index'), 'pattern' => 'technician.equipment.*', 'route' => true],
            ['label' => 'Equipment Assignments', 'href' => route('technician.assignments.index'), 'pattern' => 'technician.assignments.*', 'route' => true],
        ]],
        ['label' => 'Service Operations', 'items' => [
            ['label' => 'Faults / Incidents', 'href' => route('technician.faults.index'), 'pattern' => 'technician.faults.*', 'route' => true],
            ['label' => 'Maintenance & Repairs', 'href' => route('technician.maintenance.index'), 'pattern' => 'technician.maintenance.*', 'route' => true],
            ['label' => 'Inspections', 'href' => route('technician.inspections.index'), 'pattern' => 'technician.inspections.*', 'route' => true],
            ['label' => 'Transfers', 'href' => route('technician.transfers.index'), 'pattern' => 'technician.transfers.*', 'route' => true],
        ]],
        ['label' => 'Records & Insights', 'items' => [
            ['label' => 'Documents', 'href' => route('technician.documents.index'), 'pattern' => 'technician.documents.*', 'route' => true],
            ['label' => 'ICT Reports', 'href' => route('technician.reports.index'), 'pattern' => 'technician.reports.*', 'route' => true],
        ]],
    ];
@endphp

<x-sidebar :navigation-groups="$technicianNavigation" aria-label="IT Technician navigation" sidebar-id="technician-sidebar" />
@endif
