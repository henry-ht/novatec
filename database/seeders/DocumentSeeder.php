<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datos = [
            array(
                'name'          => 'Cedula de ciudadanía',
                'abbr'          => 'CC',
                'type'          => 'identification'
            ),
            array(
                'name'          => 'Cedula de extranjería',
                'abbr'          => 'CE',
                'type'          => 'identification'
            ),
            array(
                'name'          => 'Documento nacional de identidad',
                'abbr'          => 'DNI',
                'type'          => 'identification'
            ),
        ];

        foreach ($datos as $key => $value) {
            Document::updateOrCreate([
                'name' => $value['name']
            ], $value);
        }
    }
}
