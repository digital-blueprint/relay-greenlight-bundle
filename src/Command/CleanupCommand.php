<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Command;

use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupCommand extends Command
{
    protected static $defaultName = 'dbp:relay-greenlight:cleanup';

    /**
     * @var GreenlightService
     */
    private $greenlightService;

    public function __construct(GreenlightService $greenlightService)
    {
        parent::__construct();

        $this->greenlightService = $greenlightService;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Removes expired Permit entities in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Removing expired Permit entities...');
        $reviews = $this->greenlightService->getExpiredPermits();
        $reviewCount = count($reviews);

        if ($reviewCount === 0) {
            $output->writeln('There were no expired reviews.');
        } else {
            foreach ($reviews as $review) {
                $this->greenlightService->removePermit($review);
            }

            $output->writeln($reviewCount.' expired reviews where removed.');
        }

        return 0;
    }
}
