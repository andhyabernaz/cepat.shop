<?php

namespace App\Services\Payment;

interface PaymentGateway
{
   public function createTransaction(array $params): array;
   public function paymentChanels(?array $params): array;
}