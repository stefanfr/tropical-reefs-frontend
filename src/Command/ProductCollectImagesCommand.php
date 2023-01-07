<?php

namespace App\Command;

use App\Entity\Manager\Product\Images;
use App\Repository\Manager\Product\DraftRepository;
use App\Repository\Manager\Product\ImagesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:product:collect:images',
    description: 'Collect images from dir and match to product',
)]
class ProductCollectImagesCommand extends Command
{
    public function __construct(
        protected ContainerInterface $container,
        protected ManagerRegistry    $managerRegistry,
        protected DraftRepository    $draftRepository,
        protected ImagesRepository   $imagesRepository,
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

        $em = $this->managerRegistry->getManager('manager');
        $imagePath = $this->container->getParameter('kernel.project_dir') . '/assets/products/images/unprocessed';
        $processedImagesPath = $this->container->getParameter('kernel.project_dir') . '/assets/products/images/processed';
        $files = [];
        foreach (scandir($imagePath) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $files[] = $file;
        }

        $products = $this->draftRepository->findBy(
            [
                'category' => 'AQUAFOREST',
            ]
        );

        foreach ($products ?? [] as $product) {
            $images = array_filter($files, static function ($file) use ($product) {
                $_name = trim(preg_replace('/\d/', '', str_replace(['AF-', 'AF_', 'AF ', 'Stk.'], '', $product->getName())));
                $_file = trim(preg_replace('/\d/', '', str_replace(['-', '_'], ' ', str_replace(['AF-', 'AF_', 'AF ', '.png', 'mockup'], '', $file))));

                dump(
                    [
                        strcasecmp($_name, $_file),
                        $_name,
                        $_file,
                        $file,
                    ]
                );
                return
                    ($product->getEan() && str_contains($file, $product->getEan()))
                    || strcasecmp($_name, $_file) === 0;
            });

            if (empty($images)) {
                continue;
            }

            dump($images, $product->getName());

            foreach (array_filter($images) ?? [] as $image) {
                $newImage = $this->imagesRepository->findOneBy(
                    [
                        'product' => $product,
                        'filename' => $image,
                    ]
                ) ?: new Images();

                $newImage
                    ->setProduct($product)
                    ->setFilename($image);
                $em->persist($newImage);

                try {
                    rename(
                        $imagePath . '/' . $image,
                        $processedImagesPath . '/' . $image,
                    );
                } catch (\Exception $exception) {
                    $io->error($exception->getMessage());
                    continue;
                }
            }

            $em->flush();
        }

        $io->success('Successfully collected all available images');

        return Command::SUCCESS;
    }
}
