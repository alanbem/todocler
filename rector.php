<?php

/**
 * This file is part of the todocler package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator) : void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // Define what rule sets will be applied
    $parameters->set(Option::SETS, [
        SetList::PHPUNIT_CODE_QUALITY,
        SetList::PHPUNIT_80,
        //        SetList::PHPUNIT_SPECIFIC_METHOD,
        //        SetList::CODE_QUALITY,
        //        SetList::DEAD_CODE_STRICT,
        SetList::PHP_74,
    ]);

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [__DIR__.'/src', __DIR__.'/tests']);

//    // auto import fully qualified class names? [default: false]
//    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

//    // skip root namespace classes, like \DateTime or \Exception [default: true]
//    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
};
