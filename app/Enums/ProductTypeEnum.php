<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
   case Digital = 'Digital';
   case Deposit = 'Deposit';
   case DigitalDownload = 'Digital Download';
   case DigitalVideo = 'Digital Video';

   public static function isDigital(string $needle): bool
   {
      $values = array_filter(self::getValues(), function($v) {
         return str_contains($v, 'Digital');
      });

      return in_array($needle, $values);
   }

   public static function getValues(): array
   {
      return array_map(fn($case) => $case->value, self::cases());
   }
}
