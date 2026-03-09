<?php

return [

   'requirements' => [
      'enabled'           => ':feature perlu diaktifkan!',
      'disabled'          => ':feature perlu dinonaktifkan!',
      'extension'         => ':extension ekstensi perlu diinstal dan dimuat!',
      'directory'         => ':directory direktory not writable!',
      'executable'        => 'Berkas PHP CLI belum didefinisikan/bekerja atau bukan versi :php_version ke atas. Mohon tanyakan ke penyedia hosting untuk mengatur variabel PHP_BINARY atau PHP_PATH dengan benar.',
      'npm'               => '<b>File JavaScript tidak ada !</b> <br><br><span>Anda harus menjalankan <em class="underline">npm install</em> dan <em class="underline">npm run dev< /em> perintah.</span>',
   ],

   'database' => [
      'hostname'          => 'Nama host',
      'username'          => 'Nama pengguna',
      'password'          => 'Kata Sandi',
      'name'              => 'Database',
   ],

   'error' => [
      'php_version'       => 'Terjadi Kesalahan: Versi PHP tidak kompatibel, gunakan PHP :php_version atau lebih tinggi.',
      'connection'        => 'Kesalahan: Tidak dapat terhubung ke database! Silahkan, pastikan bahwa rinciannya benar.',
   ],

];
