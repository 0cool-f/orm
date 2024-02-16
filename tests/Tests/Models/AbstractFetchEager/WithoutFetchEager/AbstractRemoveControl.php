<?php

declare(strict_types=1);

namespace Doctrine\Tests\Models\AbstractFetchEager\WithoutFetchEager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"mobile"="MobileRemoteControl"})
 */
abstract class AbstractRemoveControl
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    public $id;

    /**
     * /**
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="remoteControl")
     *
     * @var Collection<User>
     */
    public $users;

    public function __construct(string $name)
    {
        $this->name  = $name;
        $this->users = new ArrayCollection();
    }
}
