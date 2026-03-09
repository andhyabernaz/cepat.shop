<?php

namespace App\Traits;

use App\Scopes\SortOrderedScope;

trait Sortable
{
   public static function bootSortable()
   {
      static::addGlobalScope(new SortOrderedScope);
   }
}
