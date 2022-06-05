<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccompagnateurTableSeeder extends Seeder
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
         DB::table('accompagnateurs')->insert([
            
            'nomArabe'=>' بن علي',
            'prenomArabe'=>'اكرم',
            'telephoneTunisien'=>'50252121',
            'user_id'=>4,
            'image'=>'images/accompagnateurs/accompgnateur1.jpg',
          
        ]);
        DB::table('accompagnateurs')->insert([
           
            'nomArabe'=>' بن سالم',
            'prenomArabe'=>'سالم',
            'telephoneTunisien'=>'94250241',
            'user_id'=>5,
            'image'=>'images/accompagnateurs/accompgnateur2.jpg',
            
        ]);
    }
}
