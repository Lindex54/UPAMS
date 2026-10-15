<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\District;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Seed Busitema University's operating campuses.
     *
     * Coordinates are town-level reference points and should be refined from on-site GPS readings.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Main Campus', 'code' => 'MAIN', 'district' => 'Busia', 'location' => 'Busitema, Jinja–Tororo Highway', 'faculty' => 'Faculty of Engineering and Technology', 'latitude' => 0.5450000, 'longitude' => 34.0250000],
            ['name' => 'Nagongera Campus', 'code' => 'NAG', 'district' => 'Tororo', 'location' => 'Tororo–Busolwe Road, Nagongera', 'faculty' => 'Faculty of Science and Education', 'latitude' => 0.7700000, 'longitude' => 34.0261000],
            ['name' => 'Arapai Campus', 'code' => 'ARP', 'district' => 'Soroti', 'location' => 'Soroti–Moroto Road, Arapai', 'faculty' => 'Faculty of Agriculture and Animal Sciences', 'latitude' => 1.7800000, 'longitude' => 33.6250000],
            ['name' => 'Namasagali Campus', 'code' => 'NAM', 'district' => 'Kamuli', 'location' => 'Former Namasagali University, Namasagali', 'faculty' => 'Faculty of Natural Resources and Environmental Sciences', 'latitude' => 1.0125000, 'longitude' => 32.9500000],
            ['name' => 'Mbale Campus', 'code' => 'MBL', 'district' => 'Mbale', 'location' => 'Mbale Regional Referral Hospital, Mbale City', 'faculty' => 'Faculty of Health Sciences', 'latitude' => 1.0805560, 'longitude' => 34.1750000],
            ['name' => 'Pallisa Campus', 'code' => 'PAL', 'district' => 'Pallisa', 'location' => 'Pallisa Town', 'faculty' => 'Faculty of Management Sciences', 'latitude' => 1.1675000, 'longitude' => 33.7100000],
        ] as $campus) {
            $districtName = $campus['district'];
            unset($campus['district']);

            Campus::query()->updateOrCreate(
                ['name' => $campus['name']],
                $campus + [
                    'district_id' => District::query()->where('name', $districtName)->value('id'),
                    'status' => Campus::STATUS_ACTIVE,
                ],
            );
        }
    }
}
