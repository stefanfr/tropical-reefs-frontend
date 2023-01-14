<?php

namespace App\Command\Manager\Generate;

use App\Entity\Manager\GeneratedProduct;
use App\Entity\Manager\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'manager:generate:products',
    description: 'Convert products to product and model pairs',
)]
class GenerateProductCommand extends Command
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
        $io = new SymfonyStyle($input, $output);
        $em = $this->managerRegistry->getManager('manager');
        $generatedProductRepository = $this->managerRegistry->getManager('manager')
            ->getRepository(GeneratedProduct::class);
        $products = $this->managerRegistry->getManager('manager')->getRepository(Product::class)->findBy(
            [
                'parent' => null,
            ]
        );

        foreach ($products ?? [] as $product) {
            $generatedProduct = $generatedProductRepository->findOneBy(
                [
                    'supplierCode' => $product->getSupplierCode(),
                ]
            );

            if (null === $generatedProduct) {
                $generatedProduct = new GeneratedProduct();
            }
            $productData = $product->getProductData();
            $parsedName = str_replace(' ', '_', strtolower($productData['category']));
            if ( ! isset(self::$lastSku[$product->getBrand()])) {
                self::$lastSku[$product->getBrand()] = 1;
            }

            $skuNr = self::$lastSku[$product->getBrand()]++;
            $sku = self::$skuMapping[$product->getBrand()] . '-' . str_pad($skuNr, 4, '0', STR_PAD_LEFT);
            $name = trim(str_replace([$product->getBrand(), strtolower($product->getBrand()), strtoupper($product->getBrand())], '', $productData['name']));
            $name = preg_replace('/(- |-Futter |-Futter\/Granulat |-Futter\/Pulver |, )/', '', $name);
            $name = preg_replace("/[\n\r]/", ' ', $name);
            $generatedProduct
                ->setSku($sku)
                ->setBrand($parsedName)
                ->setEnabled(1)
                ->setProductEnabled(1)
                ->setCategories($parsedName)
                ->setFamily('products')
                ->setName(trim($name))
                ->setEanCode($productData['ean'])
                ->setWeight($productData['weight'])
                ->setPurchasePrice($productData['npp'])
                ->setSupplierCode($product->getSupplierCode())
                ->setTax($productData['tax'] === '1' ? 'HIGH' : 'LOW')
                ->setSalesPrice($productData['rrp'] ?: $productData['npp'] * 2);

            $em->persist($generatedProduct);
            $em->flush();
        }

        return 0;
    }
}