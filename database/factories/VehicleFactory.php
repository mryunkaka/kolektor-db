<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'no_kontrak' => $this->faker->unique()->numerify('##########'), // Contoh: 4232403442
            'nama_konsumen' => strtoupper($this->faker->name()), // Contoh: AZIZ MAULANA
            'no_polisi' => strtoupper($this->faker->bothify('B####???')), // Contoh: B9466SAL
            'no_rangka' => strtoupper($this->faker->bothify('??#??##??#######')), // Contoh: MK2L0PU39MJ023448
            'no_mesin' => strtoupper($this->faker->bothify('#####??####')), // Contoh: 4D56CXX6507
            'merk_tipe' => $this->faker->randomElement([
                'MITSUBISHI COLT L 300-DIESEL 2.5 PU FLAT DECK',
                'TOYOTA AVANZA 1.3 G M/T',
                'HONDA BEAT ESP CBS',
                'SUZUKI ERTIGA GX AT',
                'YAMAHA NMAX 155'
            ]),
            'past_due' => $this->faker->numberBetween(0, 180), // Contoh: 81
            'nama_resort' => $this->faker->randomElement([
                'RES KALBAGBAR',
                'RES JABODETABEK',
                'RES JATIM',
                'RES SULSEL'
            ]),
            'nama_sector' => $this->faker->randomElement([
                'SEC BANJARMASIN',
                'SEC JAKARTA',
                'SEC SURABAYA'
            ]),
            'nama_sub_sector' => $this->faker->randomElement([
                'SCG BANJARBARU',
                'SCG CENGKARENG',
                'SCG MALANG'
            ]),
            'product' => $this->faker->randomElement([
                'NDF Car',
                'NDF Motorcycle',
                'Multiguna Mobil',
                'Multiguna Motor'
            ]),
        ];
    }
}
