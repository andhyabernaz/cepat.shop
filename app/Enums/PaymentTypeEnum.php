<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
   case PAYMENT_GATEWAY = 'PAYMENT_GATEWAY';
   case PAYMEMT_DIRECT_TRANSFER = 'DIRECT_TRANSFER';
   case PAYMEMT_CASH = 'CASH';
   case PAYMEMT_COD = 'COD';
   case PAYMEMT_SALDO_BALANCE = 'SALDO_BALANCE';
}
