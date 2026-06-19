<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Index untuk query harga per komoditas per tanggal
        DB::statement('CREATE INDEX IF NOT EXISTS idx_komoditas_master_tanggal ON komoditas (komoditas_master_id, tanggal) INCLUDE (harga, pasar_id)');

        // Index untuk JOIN komoditas -> pasar
        DB::statement('CREATE INDEX IF NOT EXISTS idx_komoditas_pasar_id ON komoditas (pasar_id)');

        // Index untuk JOIN pasar -> kab_kota
        DB::statement('CREATE INDEX IF NOT EXISTS idx_pasar_kabkota_id ON pasar (kabkota_id)');

        // Index untuk JOIN kab_kota -> provinsi
        DB::statement('CREATE INDEX IF NOT EXISTS idx_kab_kota_provinsi_id ON kab_kota (provinsi_id)');

        // Spatial index untuk geometri kab_kota
        DB::statement('CREATE INDEX IF NOT EXISTS idx_kab_kota_geom ON kab_kota USING GIST (geom)');

        // Spatial index untuk geometri pasar
        DB::statement('CREATE INDEX IF NOT EXISTS idx_pasar_geom ON pasar USING GIST (geom)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_komoditas_master_tanggal');
        DB::statement('DROP INDEX IF EXISTS idx_komoditas_pasar_id');
        DB::statement('DROP INDEX IF EXISTS idx_pasar_kabkota_id');
        DB::statement('DROP INDEX IF EXISTS idx_kab_kota_provinsi_id');
        DB::statement('DROP INDEX IF EXISTS idx_kab_kota_geom');
        DB::statement('DROP INDEX IF EXISTS idx_pasar_geom');
    }
};
