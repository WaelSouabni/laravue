<?php

use Illuminate\Database\Seeder;

class PelerinTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
           //perlerins
           DB::table('pelerins')->insert([
            
            'nomArabe'=>' بن صالح',
            'prenomArabe'=>'صالح',
            'dateNaissance'=>1980-12-19,
            'telephoneTunisien'=>'25415241',
            'numeroDePassport'=>'H5854785',
            'dateExpiration'=>'2019-08-05',
            'dateEmission'=>'2024-08-05',
            'user_id'=>2,
            'package_id'=>1,
            'etat'=>'1',
            'createur_id'=>2
            
          
        ]);
        DB::table('pelerins')->insert([
            
            'nomArabe'=>' بن علي',
            'prenomArabe'=>'علي',
            'dateNaissance'=>1964-05-10,
            'telephoneTunisien'=>'28524241',
            'numeroDePassport'=>'y855485',
            'dateExpiration'=>'2020-12-19',
            'dateEmission'=>'2025-12-19',
            'user_id'=>3,
            'package_id'=>1,
            'etat'=>'1',
            'createur_id'=>2
        ]);

    }
}
