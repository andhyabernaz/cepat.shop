<?php

namespace Silehage\Cepatshop\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MetaDescriptionScope implements Scope
{
   /**
    * Apply the scope to a given Eloquent query builder.
    */
   public function apply(Builder $builder, Model $model): void
   {
      $builder->addSelectRaw("CONCAT(SUBSTRING(REGEXP_REPLACE(products.description, '<[^>]*>', ''), 1, 130),'...') AS short_description");
   }
}
