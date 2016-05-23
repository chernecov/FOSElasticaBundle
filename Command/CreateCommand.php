<?php

namespace FOS\ElasticaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\ElasticaBundle\Index\IndexManager;
use FOS\ElasticaBundle\Index\Resetter;
use Elastica\Exception\ResponseException;

/**
 * Creates an index with mapping
 *
 * @author Roman Ruskov <roman.ruskov@intexsys.lv>
 */
class CreateCommand extends ContainerAwareCommand
{
    /**
     * Index manager
     *
     * @var IndexManager
     */
    private $indexManager;

    /**
     * Resetter
     *
     * @var Resetter
     */
    private $resetter;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('fos:elastica:create')
            ->addOption('index', null, InputOption::VALUE_REQUIRED, 'The index to create')
            ->setDescription('Creates an index with mapping');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->indexManager = $this->getContainer()->get('fos_elastica.index_manager');
        $this->resetter = $this->getContainer()->get('fos_elastica.resetter');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $index = $input->getOption('index');

        try {
            $this->indexManager->getIndex($index)->refresh();
        } catch (ResponseException $e) {
            $output->writeln(sprintf('<info>Creating index</info> <comment>%s</comment>', $index));
            $this->resetter->resetIndex($index);
            $output->writeln('<info>Done</info>');
        }
    }
}
