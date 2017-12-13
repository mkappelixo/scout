<?php

namespace LaravelDoctrine\Scout\Jobs;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Laravel\Scout\EngineManager;
use LaravelDoctrine\Scout\Searchable;
use LaravelDoctrine\Scout\SearchableQueueObject;
use LaravelDoctrine\Scout\SearchableRepository;

class MakeSearchable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * An queue object so it is possible to index binary data.
     *
     * @var \LaravelDoctrine\Scout\SearchableQueueObject
     */
    public $queueObject;

    /**
     * @var Searchable
     */
    private $entity;

    /**
     * Create a new job instance.
     *
     * @param Searchable $entity
     */
    public function __construct(Searchable $entity)
    {
        $this->queueObject = new SearchableQueueObject($entity);
    }

    /**
     * @param  EngineManager          $engine
     * @param  EntityManagerInterface $em
     * @return bool|void
     */
    public function handle(EngineManager $engine, EntityManagerInterface $em)
    {
        if (empty($this->queueObject)) {
            return false;
        }

        $repository = $this->getRepository($em, $this->queueObject, $engine);

        return $repository->makeEntitiesSearchable(new Collection([$this->entity]));
    }

    /**
     * @param  EntityManagerInterface $em
     * @param  SearchableQueueObject             $object
     * @param  EngineManager          $engine
     * @return SearchableRepository
     */
    private function getRepository(EntityManagerInterface $em, SearchableQueueObject $object, EngineManager $engine)
    {
        $class      = $object->class;
        $cmd        = $em->getClassMetadata($class);
        $repository = $em->getRepository($class);

        if (!$repository instanceof SearchableRepository) {
            $repository = new SearchableRepository(
                $em,
                $cmd,
                $engine
            );
        }

        $this->entity = $repository->find($this->queueObject->id);
        $this->entity->setClassMetaData($cmd);
        $this->entity->setSearchableAs($repository->searchableAs());

        return $repository;
    }
}
