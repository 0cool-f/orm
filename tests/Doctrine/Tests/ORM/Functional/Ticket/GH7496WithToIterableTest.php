<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\Tests\IterableTester;
use Doctrine\Tests\OrmFunctionalTestCase;

final class GH7496WithToIterableTest extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEntitySchema(
            [
                GH7496EntityA::class,
                GH7496EntityB::class,
                GH7496EntityAinB::class,
            ],
        );

        $this->_em->persist($a1 = new GH7496EntityA(1, 'A#1'));
        $this->_em->persist($a2 = new GH7496EntityA(2, 'A#2'));
        $this->_em->persist($b1 = new GH7496EntityB(1, 'B#1'));
        $this->_em->persist(new GH7496EntityAinB(1, $a1, $b1));
        $this->_em->persist(new GH7496EntityAinB(2, $a2, $b1));

        $this->_em->flush();
        $this->_em->clear();
    }

    public function testNonUniqueObjectHydrationDuringIteration(): void
    {
        $q = $this->_em->createQuery(
            'SELECT b FROM ' . GH7496EntityAinB::class . ' aib JOIN ' . GH7496EntityB::class . ' b WITH aib.eB = b',
        );

        $bs = IterableTester::iterableToArray(
            $q->toIterable([], AbstractQuery::HYDRATE_OBJECT),
        );

        self::assertCount(2, $bs);
        self::assertInstanceOf(GH7496EntityB::class, $bs[0]);
        self::assertInstanceOf(GH7496EntityB::class, $bs[1]);
        self::assertEquals(1, $bs[0]->id);
        self::assertEquals(1, $bs[1]->id);

        $bs = IterableTester::iterableToArray(
            $q->toIterable([], AbstractQuery::HYDRATE_ARRAY),
        );

        self::assertCount(2, $bs);
        self::assertEquals(1, $bs[0]['id']);
        self::assertEquals(1, $bs[1]['id']);
    }
}

#[Entity]
class GH7496EntityA
{
    public function __construct(
        #[Id]
        #[Column(type: 'integer', name: 'a_id')]
        public int $id,
        #[Column(type: 'string', length: 255)]
        public string $name,
    ) {
    }
}

#[Entity]
class GH7496EntityB
{
    public function __construct(
        #[Id]
        #[Column(type: 'integer', name: 'b_id')]
        public int $id,
        #[Column(type: 'string', length: 255)]
        public string $name,
    ) {
    }
}

#[Entity]
class GH7496EntityAinB
{
    /**
     * @param GH7496EntityA $a
     * @param GH7496EntityB $b
     */
    public function __construct(
        #[Id]
        #[Column(type: 'integer')]
        public int $id,
        #[ManyToOne(targetEntity: GH7496EntityA::class)]
        #[JoinColumn(name: 'a_id', referencedColumnName: 'a_id', nullable: false)]
        public $eA,
        #[ManyToOne(targetEntity: GH7496EntityB::class)]
        #[JoinColumn(name: 'b_id', referencedColumnName: 'b_id', nullable: false)]
        public $eB,
    ) {
    }
}
