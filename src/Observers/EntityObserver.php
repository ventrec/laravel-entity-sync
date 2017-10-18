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
        /*
         * Only run if the package is enabled.
         * This makes it possible to change the config in realtime in order to prevent sync while seeding
         */
        if (config('laravelEntitySync.enabled')) {
            dispatch(new EntitySyncer($this->resolveEntityName($entity), $this->resolveEntityData($entity), 'created'));
        }
    }

    public function updated(Model $entity)
    {
        if (method_exists($entity, 'ignoreSyncAttributes')) {
            // Get the updated fields and remove updated_at and fields we would like to ignore
            $updatedFields = collect($entity->getDirty())
                ->except(array_merge($entity->ignoreSyncAttributes(), ['updated_at']));

            // If there is still updated fields, we continue with the update
            if ($updatedFields->isNotEmpty()) {
                dispatch(
                    new EntitySyncer($this->resolveEntityName($entity), $this->resolveEntityData($entity), 'updated')
                );
            }
        } else {
            dispatch(
                new EntitySyncer($this->resolveEntityName($entity), $this->resolveEntityData($entity), 'updated')
            );
        }
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

    private function resolveEntityData($entity)
    {
        $data = $entity->toArray();

        if (method_exists($entity, 'ignoreSyncAttributes')) {
            foreach ($entity->ignoreSyncAttributes() as $attribute) {
                unset($data[$attribute]);
            }
        }

        return $data;
    }
}
