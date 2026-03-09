<?php

namespace App\Enums;


enum MutasiSaldoStatusEnum: string
{
   case Success = 'Success';
   case Failed = 'Failed';
   case Rejected = 'Rejected';
}
