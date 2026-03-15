<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('affiliate_configs', function (Blueprint $table) {
            $table->text('welcome_message')->nullable();
            $table->text('suspend_message')->nullable();
        });

        $welcomeMsg = "<h4>Selamat datang di program affiliasi kami</h4>
           <p> Terima kasih telah bergabung dengan program affiliasi kami, Saat ini akun anda belum aktif. Mohon kesediaannya untuk menunggu, kami akan segera mengaktifkan akun anda.</p>";
        $susMessage = "<h4>Maaf, Akun Affiliasi anda kami tangguhkan</h4>
            <p>Kami mendeteksi adanya perilaku tidak wajar di akun anda, sehingga dengan berat hati akun anda kami tangguhkan.</p>
            <p>Silahkan hubungi kami untuk selengkapnya. terimakasih.</p>";

        DB::table('affiliate_configs')->update([
            'welcome_message' => $welcomeMsg,
            'suspend_message' => $susMessage,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_configs', function (Blueprint $table) {
            //
        });
    }
};
