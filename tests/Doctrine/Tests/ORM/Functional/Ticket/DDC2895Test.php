<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\Tests\OrmFunctionalTestCase;

use function assert;

class DDC2895Test extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchemaForModels(DDC2895::class);
    }

    public function testPostLoadOneToManyInheritance(): void
    {
        $cm = $this->_em->getClassMetadata(DDC2895::class);

        self::assertEquals(
            [
                'prePersist' => ['setLastModifiedPreUpdate'],
                'preUpdate' => ['setLastModifiedPreUpdate'],
            ],
            $cm->lifecycleCallbacks,
        );

        $ddc2895 = new DDC2895();

        $this->_em->persist($ddc2895);
        $this->_em->flush();
        $this->_em->clear();

        $ddc2895 = $this->_em->find($ddc2895::class, $ddc2895->id);
        assert($ddc2895 instanceof DDC2895);

        self::assertNotNull($ddc2895->getLastModified());
    }
}

#[MappedSuperclass]
#[HasLifecycleCallbacks]
abstract class AbstractDDC2895
{
    /**
     * @var DateTime
     */
    #[Column(name: 'last_modified', type: 'datetimetz', nullable: false)]
    protected $lastModified;

    #[PrePersist]
    #[PreUpdate]
    public function setLastModifiedPreUpdate(): void
    {
        $this->setLastModified(new DateTime());
    }

    public function setLastModified(DateTime $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    public function getLastModified(): DateTime
    {
        return $this->lastModified;
    }
}

#[Entity]
#[HasLifecycleCallbacks]
class DDC2895 extends AbstractDDC2895
{
    /**
     * @var int
     */
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    public $id;

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }
}
