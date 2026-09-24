<?php

$text = static fn (string $key, string $label, string $placeholder = ''): array => compact('key', 'label', 'placeholder') + ['type' => 'text'];
$number = static fn (string $key, string $label, string $placeholder = ''): array => compact('key', 'label', 'placeholder') + ['type' => 'number'];
$select = static fn (string $key, string $label, array $options): array => compact('key', 'label', 'options') + ['type' => 'select'];

$computerCore = [
    $text('processor', 'Processor', 'Intel Core i5 / AMD Ryzen 5'),
    $text('processor_generation', 'Processor Generation / Model'),
    $text('ram_size', 'RAM Size', '16 GB'),
    $text('ram_type', 'RAM Type', 'DDR4'),
    $text('storage_capacity', 'Storage Capacity', '512 GB'),
    $select('storage_type', 'Storage Type', ['HDD', 'SSD', 'NVMe', 'Hybrid']),
    $text('graphics', 'Graphics'),
    $text('operating_system', 'Operating System'),
    $text('network_mac', 'Network / MAC Address'),
];
$computerSubtypes = ['Desktop', 'Laptop', 'Workstation', 'All-in-One'];
$broadComputerCore = array_map(
    static fn (array $field): array => $field + ['show_when' => ['key' => 'computer_subtype', 'values' => $computerSubtypes]],
    $computerCore,
);

return [
    'type_profiles' => [
        'Desktop Computer' => 'desktop', 'Laptop' => 'laptop', 'Monitor' => 'monitor',
        'Printer / Scanner' => 'printer_scanner', 'Projector' => 'projector', 'Server' => 'server',
        'UPS / Power Equipment' => 'ups', 'Network Equipment' => 'network',
        'Communication Equipment' => 'communication', 'ICT Peripheral' => 'peripheral',
        'Computer Equipment' => 'computer_equipment',
    ],
    'profiles' => [
        'desktop' => $computerCore,
        'laptop' => [...$computerCore, $text('screen', 'Screen Size / Resolution'), $text('battery', 'Battery Details')],
        'monitor' => [$text('screen', 'Screen Size'), $text('resolution', 'Resolution'), $text('panel_type', 'Panel Type'), $text('refresh_rate', 'Refresh Rate'), $text('video_ports', 'Video Ports')],
        'printer_scanner' => [$select('device_subtype', 'Device Subtype', ['Printer', 'Scanner', 'Multifunction']), $text('printing_technology', 'Printing Technology'), $select('colour_capability', 'Colour Capability', ['Monochrome', 'Colour']), $text('print_speed', 'Print Speed'), $select('duplex', 'Duplex', ['Automatic', 'Manual', 'Not Supported']), $text('connectivity', 'Connectivity'), $text('network_ip', 'Network / IP Address'), $text('cartridge', 'Cartridge / Toner') + ['show_when' => ['key' => 'device_subtype', 'values' => ['Printer', 'Multifunction']]], $text('scanner_details', 'Scanner Capability') + ['show_when' => ['key' => 'device_subtype', 'values' => ['Scanner', 'Multifunction']]]],
        'projector' => [$text('resolution', 'Native Resolution'), $text('brightness', 'Brightness (Lumens)'), $text('projection_technology', 'Projection Technology'), $text('ports', 'Input Ports'), $text('light_source', 'Light Source'), $number('lamp_hours', 'Lamp / Light-source Hours')],
        'server' => [$text('processors', 'Processor(s)'), $number('processor_count', 'Processor Count'), $number('core_count', 'Total Core Count'), $text('ram_size', 'RAM Size'), $text('storage_configuration', 'Storage Configuration'), $text('raid_level', 'RAID Level'), $text('operating_system', 'Operating System'), $text('hypervisor', 'Hypervisor'), $text('network_interfaces', 'Network Interfaces'), $text('rack_form_factor', 'Rack / Form Factor')],
        'ups' => [$select('ups_subtype', 'UPS Subtype', ['Offline', 'Line-interactive', 'Online']), $text('va_rating', 'VA Rating'), $text('watt_rating', 'Watt Rating'), $text('input_voltage', 'Input Voltage'), $text('output_voltage', 'Output Voltage'), $text('battery_capacity', 'Battery Capacity'), $number('battery_count', 'Battery Count'), $text('runtime', 'Estimated Runtime'), ['key' => 'battery_replacement_date', 'label' => 'Battery Replacement Date', 'type' => 'date']],
        'network' => [$select('network_subtype', 'Network Device Subtype', ['Switch', 'Router', 'Access Point', 'Firewall', 'Modem', 'Other']), $number('port_count', 'Port Count'), $text('port_speed', 'Port Speed'), $select('poe_support', 'PoE Support', ['Yes', 'No']), $text('wireless_standard', 'Wireless Standard') + ['show_when' => ['key' => 'network_subtype', 'values' => ['Router', 'Access Point', 'Modem']]], $text('management_ip', 'Management IP'), $text('mac_address', 'MAC Address'), $text('firmware_version', 'Firmware Version')],
        'communication' => [$select('communication_subtype', 'Communication Device Subtype', ['IP Phone', 'Mobile Phone', 'Radio', 'Video Conferencing', 'Other']), $text('technology', 'Technology / Network'), $text('mac_address', 'MAC Address'), $text('imei', 'IMEI'), $text('connectivity', 'Connectivity'), $text('power_details', 'Power Details')],
        'peripheral' => [
            $select('peripheral_subtype', 'Peripheral Subtype', ['Keyboard', 'Mouse', 'Webcam', 'External Drive', 'Docking Station', 'Headset', 'Other']),
            $text('interface_type', 'Interface Type') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['Keyboard', 'Mouse', 'Webcam', 'External Drive', 'Docking Station', 'Headset']]],
            $text('storage_capacity', 'Storage Capacity') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['External Drive']]],
            $text('resolution', 'Resolution') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['Webcam']]],
            $text('connectivity', 'Connectivity') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['Keyboard', 'Mouse', 'Webcam', 'Docking Station', 'Headset', 'Other']]],
            $text('compatibility', 'Compatibility') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['Webcam', 'Docking Station', 'Other']]],
            $text('power_details', 'Power Details') + ['show_when' => ['key' => 'peripheral_subtype', 'values' => ['Docking Station', 'Other']]],
        ],
        'computer_equipment' => [
            $select('computer_subtype', 'Computer Subtype', $computerSubtypes) + ['required' => true],
            ...$broadComputerCore,
            $text('screen', 'Screen Size / Resolution') + ['show_when' => ['key' => 'computer_subtype', 'values' => ['Laptop', 'All-in-One']]],
            $text('battery', 'Battery Details') + ['show_when' => ['key' => 'computer_subtype', 'values' => ['Laptop']]],
        ],
    ],
];
