<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveAndCreatedAtToMTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_topic', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
            $table->timestamp('created_at')->useCurrent()->after('is_active');
        });

        DB::table('m_topic')->update([
            'is_active' => false,
            'created_at' => '2023-08-01 00:00:00'
        ]);

        // 🔹 Tambah data baru dari array
        $topics = [
            'Advanced and Functional Materials',
            'Materials and Devices',
            'New Materials for Energy and Energy Conversion',
            'Biomaterials',
            'Theoretical/Modeling/Computer Simulations of Functional Materials',
            'Spectroscopy for Advanced Materials',
            'Hybrid and Composite Materials',
            'Magnetic Materials',
        ];

        foreach ($topics as $name) {
            DB::table('m_topic')->insert([
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('m_topic')->where('is_active', true)->delete();
        Schema::table('m_topic', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'created_at']);
        });
    }
}
