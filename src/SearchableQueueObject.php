<?php

namespace LaravelDoctrine\Scout;

/**
 * Class SearchableQueueObject
 * @package LaravelDoctrine\Scout
 */
class SearchableQueueObject{

    /**
     * The class name of the model.
     *
     * @var string
     */
    public $class;

    /**
     * The unique identifier of the model.
     *
     * This may be either a single ID or an array of IDs.
     *
     * @var mixed
     */
    public $id;

    /**
     * Create a new model identifier.
     *
     * @param  string  $class
     * @param  mixed  $id
     * @return void
     */
    public function __construct(Searchable $entity)
    {
        $this->id = $entity->getKey();
        $this->class = get_class($entity);
    }
}