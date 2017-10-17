<?php

namespace Ventrec\LaravelEntitySync\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ventrec\LaravelEntitySync\Jobs\EntitySyncer;
use Ventrec\LaravelEntitySync\Jobs\SyncDeletedEntity;

class EntityObserver
{
    public function created(Model $entity)
    {
        dispatch(new EntitySyncer($this->resolveEntityName($entity), $this->resolveEntity($entity), 'created'));
    }

    public function updated(Model $entity)
    {
        dispatch(new EntitySyncer($this->resolveEntityName($entity), $this->resolveEntity($entity), 'updated'));
    }

    public function deleted(Model $entity)
    {
        if (in_array(SoftDeletes::class, class_uses($entity))) {
            $forceDelete = $entity->isForceDeleting();
        } else {
            $forceDelete = false;
        }

        dispatch(new SyncDeletedEntity($this->resolveEntityName($entity), $entity->id, $forceDelete));
    }

    private function resolveEntityName($entity)
    {
        $class = get_class($entity);
        $data = substr($class, (strrpos($class, '\\') + 1));

        return camel_case($data);
    }

    private function resolveEntity($entity)
    {
        if (method_exists($entity, 'ignoreSyncAttributes')) {
            foreach ($entity->ignoreSyncAttributes() as $attribute) {
                unset($entity->{$attribute});
            }
        }

        return $entity;
    }
}
