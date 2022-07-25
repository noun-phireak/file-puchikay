<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert(
            [
                [ 'username'=>'fileuser', 'password' => bcrypt('F!LEWQ12')],
               
                
            ]);
      
    }
}
