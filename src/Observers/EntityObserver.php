<?php

namespace Ventrec\LaravelEntitySync\Observers;

use Illuminate\Database\Eloquent\Model;
use Ventrec\LaravelEntitySync\Jobs\EntitySyncer;

class EntityObserver
{
    public function created(Model $entity)
    {
        dispatch(new EntitySyncer($entity, $this->resolveEntityName($entity), 'created'));
    }

    public function updated(Model $entity)
    {
        dispatch(new EntitySyncer($entity, $this->resolveEntityName($entity), 'updated'));
    }

    public function deleted(Model $entity)
    {
        dispatch(new EntitySyncer($entity, $this->resolveEntityName($entity), 'deleted'));
    }

    private function resolveEntityName($entity)
    {
        $class = get_class($entity);
        $data = substr($class, (strrpos($class, '\\') + 1));

        return camel_case($data);
    }
}
