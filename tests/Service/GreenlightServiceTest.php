<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests\Service;

use Dbp\Relay\BasePersonBundle\Entity\Person;
use Dbp\Relay\BasePersonBundle\Service\DummyPersonProvider;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Dbp\Relay\GreenlightBundle\Service\VizHashProvider;
use Dbp\Relay\GreenlightBundle\TestUtils\DummyPersonPhotoProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GreenlightServiceTest extends WebTestCase
{
    /**
     * @var GreenlightService
     */
    private $greenlightService;

    protected function setUp(): void
    {
        $person = new Person();
        $person->setGivenName('Häns Rudolf');
        $person->setFamilyName('Tester Straß');
        $person->setBirthDate('1980-06-05');
        $personProvider = new DummyPersonProvider();
        $personProvider->setCurrentPerson($person);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $personPhotoProvider = new DummyPersonPhotoProvider();
        $vizHashProvider = $this->createMock(VizHashProvider::class);

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturnOnConsecutiveCalls($entityManager);

        $this->greenlightService = new GreenlightService(
            $personProvider, $managerRegistry, $personPhotoProvider, $vizHashProvider);
    }

    public function testNothing()
    {
        $this->assertTrue(true);
    }
}
