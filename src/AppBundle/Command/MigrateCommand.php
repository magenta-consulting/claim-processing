<?php

namespace AppBundle\Command;

use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:update')
            ->setDescription('Migrate data')
            ->addArgument(
                'name', InputArgument::OPTIONAL, 'Who do you want to greet?'
            )
            ->addOption(
                'yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $claims = $em->getRepository('AppBundle\Entity\Claim')->findAll();
        foreach ($claims as $claim) {
            $em->remove($claim);
        }
        $em->flush();
        $output->writeln('Update successfully.');


    }

}
