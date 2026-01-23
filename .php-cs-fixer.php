<?php

declare(strict_types=1);

// return (new PhpCsFixer\Config())
//     ->setRules([
//         '@PSR12' => true,
//         'array_syntax' => ['syntax' => 'short'],           // [] вместо array()
//         'array_indentation' => true,                       // правильные отступы
//         'trim_array_spaces' => true,                       // нет пробелов после [ и перед ]
//         'trailing_comma_in_multiline' => ['elements' => ['arrays']], // запятая после последнего
//         'single_quote' => true,
//         'no_whitespace_before_comma_in_array' => true,
//         'no_whitespace_in_blank_line' => true,
//     ])
//     ->setFinder(
//         PhpCsFixer\Finder::create()
//             ->in(__DIR__)
//             ->exclude(['vendor', 'storage', 'bootstrap/cache'])
//     );


use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
    ->setRiskyAllowed(false)
    ->setRules([
        '@auto' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],           // [] вместо array()
        'array_indentation' => true,                       // правильные отступы
        'trim_array_spaces' => true,                       // нет пробелов после [ и перед ]
        'trailing_comma_in_multiline' => ['elements' => ['arrays']], // запятая после последнего
        'single_quote' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
    ])
    // 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
    ->setFinder(
        (new Finder())
            // 💡 root folder to check
            ->in(__DIR__)
            // 💡 additional files, eg bin entry file
            // ->append([__DIR__.'/bin-entry-file'])
            // 💡 folders to exclude, if any
            // ->exclude([/* ... */])
            ->exclude(['vendor', 'storage', 'bootstrap/cache'])
        // 💡 path patterns to exclude, if any
        // ->notPath([/* ... */])
        // 💡 extra configs
        // ->ignoreDotFiles(false) // true by default in v3, false in v4 or future mode
        // ->ignoreVCS(true) // true by default
    )
;
