<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'app:localize:vue',
    description: 'Convert translations to Vue translation files.',
)]
class LocalizeVueCommand extends Command
{
    public function __construct(
        protected readonly KernelInterface     $kernel,
        protected readonly TranslatorInterface $translator,
        string                                 $name = null
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
        $io->title('Converting translations to Vue translation files');

        foreach (glob($this->kernel->getProjectDir() . '/translations/*.yaml') as $file) {
            $fileInfo = new \SplFileInfo($file);
            [$domain, $locale, $extension] = explode('.', $fileInfo->getFilename());
            $fullLocale = match ($locale) {
                'en' => 'en_GB',
                'nl' => 'nl_NL',
            };

            $contents = explode(PHP_EOL, file_get_contents($file));
            $translations = [];
            foreach ($contents as $line) {
                if (!$line) {
                    continue;
                }
                [$slug, $translation] = explode(': ', $line);
                $translation = str_replace(['"', "'"], '', $translation);
                $translations[$slug] = $translation;
            }

            $translationString = 'export default ' . json_encode($translations, JSON_PRETTY_PRINT);

            file_put_contents(
                sprintf(
                    '%s/%s/%s.ts',
                    $this->kernel->getProjectDir(),
                    'assets/i18n',
                    $fullLocale
                ), $translationString);
        }


        return Command::SUCCESS;
    }
}
