<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fixtures:clear',
    description: 'Supprime les données créées par les fixtures sans toucher aux autres tables'
)]
class ClearFixturesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Liste des entités créées par tes fixtures
        $entities = [
            \App\Entity\Picture::class,
            \App\Entity\Restaurant::class,
            \App\Entity\User::class
        ];

        foreach ($entities as $entityClass) {
            $repo = $this->em->getRepository($entityClass);
            $data = $repo->findAll();

            foreach ($data as $item) {
                $this->em->remove($item);
            }
        }

        $this->em->flush();

        $io->success('Toutes les données créées par les fixtures ont été supprimées.');
        return Command::SUCCESS;
    }
}
