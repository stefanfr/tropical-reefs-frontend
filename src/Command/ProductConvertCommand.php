<?php

namespace App\Command;

use App\Entity\Manager\Product\Draft;
use App\Repository\Manager\Product\DraftRepository;
use Doctrine\Persistence\ManagerRegistry;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;


#[AsCommand(
    name: 'app:product:convert',
    description: 'Convert product from supplier to usable data',
)]
class ProductConvertCommand extends Command
{
    public function __construct(
        protected ContainerInterface $container,
        protected ManagerRegistry    $managerRegistry,
        protected DraftRepository    $draftRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $csvPath = $this->container->getParameter('kernel.project_dir') . '/assets/products/pricelist.csv';

        $file = new SplFileObject($csvPath);
        $file->setFlags(SplFileObject::READ_CSV);
        $headers = [];

        $em = $this->managerRegistry->getManager('manager');

        foreach ($file as $key => $row) {
            if ( ! $key) {
                $headers = $row;
                continue;
            }

            $rawProduct = array_combine($headers, $row);

            $product = $this->draftRepository->findOneBy(
                ['sku' => $rawProduct['sku']]
            ) ?? new Draft();

            $rawProduct = array_map(static function ($value) {
                return trim($value) ?: null;
            }, $rawProduct);

            $product
                ->setSku($rawProduct['sku'])
                ->setName($rawProduct['name'])
                ->setCategory($rawProduct['category'])
                ->setEan(str_replace(' ', '', $rawProduct['ean']))
                ->setRrp($rawProduct['rrp'] ?: ($rawProduct['npp'] * 1.21) * 1.5)
                ->setNpp($rawProduct['npp'] ?: 0)
                ->setTariff($rawProduct['tariff'])
                ->setTaxClass($rawProduct['tax_class'])
                ->setWeight($rawProduct['weight'])
                ->setSize($rawProduct['size']);

            $em->persist($product);
        }
        $em->flush();


        $io->success('Successfully imported products');

        return Command::SUCCESS;
    }
}
