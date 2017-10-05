<?php

namespace Ventrec\LaravelEntitySync\Observers;

use Illuminate\Database\Eloquent\Model;
use Ventrec\LaravelEntitySync\Jobs\EntitySyncer;

class EntityObserver
{
    public function created(Model $entity)
    {
        dispatch(new EntitySyncer($entity, 'created'));
    }

    public function updated(Model $entity)
    {
        dispatch(new EntitySyncer($entity, 'updated'));
    }

    public function deleted(Model $entity)
    {
        dispatch(new EntitySyncer($entity, 'deleted'));
    }
}
