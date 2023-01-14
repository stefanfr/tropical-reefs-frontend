<?php

namespace App\Command\Manager\Create\Product;

use App\Entity\Manager\Product;
use App\Entity\Manager\ProductModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'manager:create:product:models',
    description: 'Convert products to product and model pairs',
)]
class CreateModelsCommand extends Command
{
    public function __construct(
        protected ManagerRegistry $managerRegistry,
        string                    $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $em = $this->managerRegistry->getManager('manager');
        $productRepository = $this->managerRegistry->getManager('manager')->getRepository(Product::class);
        $products = $productRepository->findBy(
            [
                'parent' => null,
            ]
        );

        $products = array_map(static function ($product) {
            return [
                'id' => $product->getId(),
                'brand' => $product->getBrand(),
                'parent' => $product->getParent()?->getId(),
                'name' => $product->getProductData()['name'] ?? '',
            ];
        }, $products);

        foreach ($products ?? [] as $product) {
            $parentName = null;
            $nameParts = explode(' ', trim($product['name']));
            $names = [trim($product['name'])];
            try {
                for ($i = count($nameParts); $i > 2; $i--) {
                    array_pop($nameParts);
                    $names[] = implode(' ', $nameParts);
                }

                foreach ($names as $name) {
                    $relatedProducts = $this->collectRelatedProducts($name);
                    if (count($relatedProducts) > 1 && count(array_unique(array_column($relatedProducts, 'brand'))) === 1) {
                        $parentName = $name;
                        break;
                    }
                }

                if (null === $parentName) {
                    continue;
                }

                $parentProduct = current($relatedProducts);
                $parentId = max(array_column($relatedProducts, 'parent_id'));

                if ( ! $parentId) {
                    $productModel = new ProductModel();
                } else {
                    $productModel = $this->managerRegistry->getManager('manager')->find(ProductModel::class, $parentId);
                }

                $productModel->setBrand($parentProduct['brand']);

                foreach ($relatedProducts as $_product) {
                    $childProduct = $this->managerRegistry->getManager('manager')->find(Product::class, $_product['id']);
                    $_productData = $_childProductData = $childProduct->getProductData();

                    if ( ! $productModel->getProductData()) {
                        $_productData['name'] = $parentName;
                        $productModel->setProductData($_productData);
                    }
                    $_childProductData['product_code'] = trim(str_replace($parentName, '', $_childProductData['name']));
                    $childProduct
                        ->setProductData($_childProductData)
                        ->setParent($productModel);
                    $em->persist($productModel);
                }
                $em->flush();
            } catch (\Exception $exception) {
                dd($exception->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    protected function collectRelatedProducts(string $name): array
    {
        /**
         * @var Connection $queryBuilder
         */
        $queryBuilder = $this->managerRegistry->getConnection('default');

        try {
            return $queryBuilder->createQueryBuilder()
                ->select('*')
                ->from('manager_product', 'product')
                ->where('product_data->"$.name" LIKE "%' . $name . '%"')
                ->fetchAllAssociative();
        } catch (\Exception $e) {
            return [];
        }
    }
}
