<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiResponse
{
   static private $http_status;
   static private $event;
   static private $message;

   public static function withStatusCode($http_status)
   {
      self::$http_status = $http_status;
      return new static(self::class);
   }
   public static function withEvent($event = null)
   {
      self::$event = $event;
      return new static(self::class);
   }
   public static function withMessage($message)
   {
      self::$message = $message;
      return new static(self::class);
   }

   public static function success($value = null, $msg = null)
   {
      if ($msg) {
         self::$message = $msg;
      }
      $response = [];
      $response['success'] = true;
      $response['message'] = self::$message ?? 'Request Success';
      $response['data'] = $value;

      if (!is_null($value)) {
         if (is_array($value)) {
            $response['data'] = $value;
         } elseif (is_object($value)) {
            $response['data'] = $value;
         } else {
            $response['data'] = $value;
         }
      }

      self::$http_status = self::$http_status ?? 200;

      return self::send($response);
   }

   public static function failed($error = null)
   {
      if (env('APP_ENV', 'local') != 'production') {
         Log::debug($error);
      }
      $response = [];
      $response['success'] = false;
      $response['data'] = null;
      $response['message'] = null;
      $response['errors'] = [];

      if ($error instanceof ValidationException) {
         self::$http_status = self::$http_status ?? 422;
         $response['message'] = 'Data yang input tidak valid';
         $response['errors'] = $error->errors();
      } elseif ($error instanceof QueryException) {
         $errors = [];
         if (env('APP_ENV', 'local') != 'production') {
            $errors['code'][] = $error->getCode();
            $errors['sql'][] = $error->getSql();
            $errors['bindings'][] = $error->getBindings();
         }
         $response['message'] = $error->getMessage();
         $response['errors'] = $errors;
      } elseif ($error instanceof ModelNotFoundException || $error instanceof NotFoundHttpException) {
         $errors = [];
         self::$http_status = self::$http_status ?? 404;
         $response['message'] = 'Data not found';
      } elseif ($error instanceof Exception) {
         if (env('APP_DEBUG') == true) {
            $response['message'] = $error->getMessage();
            $response['errors'] = json_decode(json_encode($error->getTrace()));
         } else {
            $response['message'] = $error->getMessage();
         }
      } elseif ($error instanceof ConnectException || $error instanceof ConnectionException) {
         if (env('APP_DEBUG') == true) {
            // $response['message'] = $error->getMessage();
            $response['errors'] = json_decode(json_encode($error->getTrace()));
            $response['message'] = "Koneksi sedang offline";
         } else {
            $response['message'] = "Koneksi sedang offline";
         }
      } else {
         if (is_object($error) || is_array($error)) {
            $response['message'] = json_encode($error);
         } else {
            $response['message'] = $error;
         }
      }

      self::$http_status = self::$http_status ?? 400;

      return self::send($response);
   }

   private static function send($data)
   {
      $httpStatus = self::$http_status;
      $event = self::$event;

      if ($event) {
         $data['event'] = $event;
      }

      self::$http_status = null;
      self::$event = null;
      self::$message = null;

      return response()->json($data, $httpStatus, [], JSON_UNESCAPED_UNICODE);
   }
}
