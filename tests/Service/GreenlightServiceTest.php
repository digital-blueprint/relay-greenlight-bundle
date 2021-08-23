<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests\Service;

use DBP\API\BaseBundle\Entity\Person;
use DBP\API\BaseBundle\TestUtils\DummyPersonProvider;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
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
        $personProvider = new DummyPersonProvider($person);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $personPhotoProvider = new DummyPersonPhotoProvider();

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturnOnConsecutiveCalls($entityManager);

        $this->greenlightService = new GreenlightService($personProvider, $managerRegistry, $personPhotoProvider);
    }

    public function testNothing()
    {
        $this->assertTrue(true);
    }
}
