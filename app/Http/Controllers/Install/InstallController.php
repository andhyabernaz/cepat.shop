<?php

namespace App\Http\Controllers\Install;

use App\Helpers\ApiResponse;
use App\Utilities\Installer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
   public function index()
   {
      return View::vue([
         'title' => 'Install',
      ]);
   }
   public function status()
   {
      return response()->json([
         'installed' => config('installer.installed')
      ]);
   }
   public function serverRequirements()
   {
      // Check requirements
      $data = Installer::checkServerRequirements();

      if (empty($data)) {
         // Create the .env file
         if (!File::exists(base_path('.env'))) {
            Installer::createDefaultEnvFile();
         }
      }

      return ApiResponse::success($data);
   }

   public function databaseIndex()
   {
      $data = [
         'host'      => env('DB_HOST', 'localhost'),
         'database'  => '',
         'username'  => '',
         'password'  => '',
      ];

      return ApiResponse::success($data);
   }

   public function databaseStore(Request $request)
   {
      $request->validate([
         'host' => 'required',
         'database' => 'required',
         'password' => 'required',
         'username' => 'required',
      ]);

      $connection = config('database.default', 'mysql');

      $port     = config("database.connections.$connection.port", '3306');
      $host     = $request->host;
      $database = $request->database;
      $username = $request->username;
      $password = $request->password ?? null;

      $prefix   = config("database.connections.$connection.prefix", null);

      $response = [
         'success' => true,
         'message' => 'Success!',
         'secret_key' => config('app.secret_key')
      ];

      // Check database connection
      if (!Installer::createDbTables($host, $port, $database, $username, $password, $prefix)) {
         $response = [
            'success' => false,
            'message' => trans('install.error.connection'),
         ];
      }

      if (empty($response)) {
         $response['redirect'] = route('install.settings');
      }

      return ApiResponse::success($response);
   }

   public function install(Request $request)
   {

      try {

         $request->validate([
            "shop_name" =>  'required',
            "shop_phone" =>  'required|numeric',
            "admin_name" =>  'required',
            "admin_email" =>  'required|email',
            "admin_password" =>  'required',
            "with_demo" =>  'required|boolean',
            'secret_key' => 'required'
         ]);

         $secret = $request->secret_key;

         if ($secret != config('app.secret_key')) {
            throw new Exception('The key is not match!');
         }

         $params = [
            '--shop_name' => $request->shop_name,
            '--shop_phone' => $request->shop_phone,
            '--admin_name' => $request->admin_name,
            '--admin_email' => $request->admin_email,
            '--admin_password' => $request->admin_password,
            '--with_demo' => $request->boolean('with_demo') ? "1" : "0",
         ];

         Artisan::call('app:install', $params);

         $env = [
            'APP_NAME' => '"' . $request->shop_name . '"',
         ];

         Installer::finalTouches($env);

         return ApiResponse::success([
            'success' => true,
            'message' => 'Installasi sukses'
         ]);
      } catch (\Throwable $th) {
         return ApiResponse::success([
            'success' => false,
            'message' => $th->getMessage()
         ]);
      }
   }
}
