<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('packages')->insert([
            'labelle'=>'vol 1',
            'description'=>'test vol 1',
            'NombrePlace'=>'50',
            'NombreAcc'=>'2',
            'NombrePlaceRestant'=>'50',
            'NombreAccRestant'=>'2',
            'prix'=>3850,
            'dateDepart'=>'2022-02-01',
        ]);

        DB::table('packages')->insert([
            'labelle'=>'vol 2',
            'description'=>'test vol 2',
            'NombrePlace'=>'30',
            'NombrePlaceRestant'=>'30',
            'NombreAcc'=>'2',
            'NombreAccRestant'=>'2',
            'prix'=>3850,
            'dateDepart'=>'2022-02-16',
        ]);
        DB::table('packages')->insert([
            'labelle'=>'vol 3',
            'description'=>'test vol 3',
            'NombrePlace'=>'40',
            'NombreAcc'=>'1',
            'NombreAccRestant'=>'1',
            'NombrePlaceRestant'=>'40',
            'prix'=>4000,
            'dateDepart'=>'2022-03-01',
        ]);
    }
}
