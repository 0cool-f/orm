<?php

declare(strict_types=1);

namespace Doctrine\Tests\Models\ValueConversionType;

use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'vct_owning_manytomany_extralazy')]
#[Entity]
class OwningManyToManyExtraLazyEntity
{
    /**
     * @var string
     */
    #[Column(type: 'rot13', length: 255)]
    #[Id]
    public $id2;

    /**
     * @var Collection<int, InversedManyToManyExtraLazyEntity>
     */
    #[JoinTable(name: 'vct_xref_manytomany_extralazy')]
    #[JoinColumn(name: 'owning_id', referencedColumnName: 'id2')]
    #[InverseJoinColumn(name: 'inversed_id', referencedColumnName: 'id1')]
    #[ManyToMany(targetEntity: 'InversedManyToManyExtraLazyEntity', inversedBy: 'associatedEntities', fetch: 'EXTRA_LAZY', indexBy: 'id1')]
    public $associatedEntities;

    public function __construct()
    {
        $this->associatedEntities = new ArrayCollection();
    }
}
