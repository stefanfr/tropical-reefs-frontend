<?php

namespace App\Command\Manager\Generate;

use App\Entity\Manager\GeneratedProduct;
use App\Entity\Manager\GeneratedProductModel;
use App\Entity\Manager\Product;
use App\Entity\Manager\ProductModel;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'manager:generate:product:models',
    description: 'Convert products to product and model pairs',
)]
class GenerateProductModelCommand extends Command
{
    static protected array $skuMapping = [
        'Aquaholland' => 'AH',
        'Ama gmbh' => 'AG',
        'Aquabee' => 'AB',
        'Aquaconnect' => 'AC',
        'Aquaforest' => 'AF',
        'Aqualight' => 'AL',
        'Aquaperfekt' => 'AP',
        'Aquarium münster' => 'AM',
        'Aquarium systems' => 'AS',
        'Arcadia' => 'AD',
        'Ati' => 'AT',
        'Bubble magus' => 'BM',
        'Coral sands' => 'CS',
        'Deltec' => 'DT',
        'Deltec/jecod' => 'JC',
        'Dupla' => 'DM',
        'Easy reefs' => 'ER',
        'Easylife' => 'EL',
        'Fauna marin' => 'FM',
        'Fiap' => 'FI',
        'Flipper' => 'FL',
        'Frostfutter' => 'FF',
        'Genesis' => 'GN',
        'Giesemann' => 'GM',
        'Grotech' => 'GT',
        'Gryphon' => 'GP',
        'H&s' => 'HS',
        'Hanna instruments' => 'HA',
        'Hobby' => 'HB',
        'Itc' => 'IT',
        'Kessil' => 'KS',
        'Korallenwelt' => 'KW',
        'Korallenzucht' => 'KZ',
        'Microbe-lift' => 'MB',
        'Mrutzek' => 'MT',
        'Nightsun' => 'NS',
        'Nyos' => 'NY',
        'Os-perfekt' => 'OP',
        'Pacific sun' => 'PS',
        'Planktonplus' => 'PP',
        'Preis aquaristik' => 'PA',
        'Pvc' => 'PC',
        'Quality fish import gmbh' => 'QF',
        'Red sea' => 'RS',
        'Reef factory' => 'RF',
        'Reeftank' => 'RT',
        'Restposten' => 'RP',
        'Rowa' => 'RO',
        'Royal exclusiv' => 'RE',
        'Royal nature' => 'RN',
        'Salifert' => 'SF',
        'Sander' => 'SA',
        'Sangokai' => 'SK',
        'Schego' => 'SH',
        'Science' => 'SI',
        'Seafriendlyreef' => 'SR',
        'Sicce' => 'SC',
        'Sonderposten' => 'SP',
        'Teco' => 'TC',
        'Theiling' => 'TH',
        'Trop-electronic gmbh' => 'TE',
        'Tropic marin' => 'TM',
        'Tropical deutschland' => 'TD',
        'Tunze' => 'TZ',
        'Venotec' => 'VN',
        'Verpackung' => 'VP',
        'Vitalis' => 'VT',
        'Wiegandt gmbh' => 'WG',
        'Zoobest' => 'ZO',
        'Zubehör' => 'ZU',
    ];

    static protected array $lastSku = [];

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
        /**
         * @var Connection $qb
         */
        $qb = $this->managerRegistry->getConnection('default');
        $io = new SymfonyStyle($input, $output);
        $em = $this->managerRegistry->getManager('manager');
        $generatedProductModelRepository = $this->managerRegistry->getManager('manager')
            ->getRepository(GeneratedProductModel::class);
        $lastSkusQuery = $qb->createQueryBuilder()
            ->select('MAX(sku) as `last_sku`')
            ->from('manager_generated_product')
            ->groupBy('brand');

        foreach ($lastSkusQuery->fetchFirstColumn() ?? [] as $sku) {
            $skuParts = explode('-', $sku);
            self::$lastSku[$skuParts[0]] = (int)$skuParts[1];
        }

        $productModels = $em->getRepository(ProductModel::class)->findBy(['sku' => null]);
        foreach ($productModels as $productModel) {
            if ( ! isset(self::$lastSku[self::$skuMapping[$productModel->getBrand()]])) {
                self::$lastSku[self::$skuMapping[$productModel->getBrand()]] = 1;
            }
            self::$lastSku[self::$skuMapping[$productModel->getBrand()]]++;

            $skuNr = self::$lastSku[self::$skuMapping[$productModel->getBrand()]];
            $sku = self::$skuMapping[$productModel->getBrand()] . '-' . str_pad($skuNr, 4, '0', STR_PAD_LEFT);

            $generatedProductModel = $generatedProductModelRepository->findOneBy(
                [
                    'code' => $productModel->getSku(),
                ]
            );

            if (null === $generatedProductModel) {
                $generatedProductModel = new GeneratedProductModel();
            }
            $productData = $productModel->getProductData();
            $parsedName = str_replace(' ', '_', strtolower($productData['category']));
            $name = trim(str_replace([$productModel->getBrand(), strtolower($productModel->getBrand()), strtoupper($productModel->getBrand())], '', $productData['name']));
            $name = preg_replace('/(- |-Futter |-Futter\/Granulat |-Futter\/Pulver |, )/', '', $name);
            $name = preg_replace("/[\n\r]/", ' ', $name);

            $productModel->setSku($sku);
            $generatedProductModel
                ->setCode($sku)
                ->setName($name)
                ->setBrand($parsedName)
                ->setCategories($parsedName)
                ->setProductEnabled(1)
                ->setFamilyVariant('model_by_product_code');

            $em->persist($generatedProductModel);

            foreach ($productModel->getChildProducts() as $key => $childProduct) {
                $childProductName = trim(str_replace([$childProduct->getBrand(), strtolower($childProduct->getBrand()), strtoupper($childProduct->getBrand())], '', $childProduct->getProductData()['name']));
                $childProductName = preg_replace('/(- |-Futter |-Futter\/Granulat |-Futter\/Pulver |, )/', '', $childProductName);
                $childProductName = preg_replace("/[\n\r]/", ' ', $childProductName);

                $childSku = $sku . '-' . str_pad($key + 1, 3, '0', STR_PAD_LEFT);
                $productCode = trim(str_replace($name, '', $childProductName));
                $productCode = str_replace([' ', ',', ','], '_', $productCode);
                $generatedProduct = $this->generateChildProduct($childSku, $sku, $productCode, $childProductName, $childProduct);
                $em->persist($generatedProduct);
            }
            $em->flush();
        }

        return 1;
    }

    protected function generateChildProduct(string $sku, string $parentSku, string $productCode, string $childProductName, Product $childProduct): GeneratedProduct
    {
        $generatedProductRepository = $this->managerRegistry->getManager('manager')
            ->getRepository(GeneratedProduct::class);

        $generatedProduct = $generatedProductRepository->findOneBy(
            [
                'supplierCode' => $childProduct->getSupplierCode(),
            ]
        );
        if (null === $generatedProduct) {
            $generatedProduct = new GeneratedProduct();
        }

        $productData = $childProduct->getProductData();
        $parsedName = str_replace(' ', '_', strtolower($productData['category']));

        return $generatedProduct
            ->setSku($sku)
            ->setBrand($parsedName)
            ->setEnabled(1)
            ->setProductEnabled(1)
            ->setProductCode($productCode)
            ->setCategories($parsedName)
            ->setFamily('products')
            ->setName(trim($childProductName))
            ->setEanCode($productData['ean'])
            ->setWeight($productData['weight'])
            ->setPurchasePrice($productData['npp'])
            ->setSupplierCode($childProduct->getSupplierCode())
            ->setTax($productData['tax'] === '1' ? 'HIGH' : 'LOW')
            ->setSalesPrice($productData['rrp'] ?: $productData['npp'] * 2)
            ->setParent($parentSku);
    }
}