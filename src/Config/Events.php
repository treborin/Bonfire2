<?php

namespace Bonfire\Config;

use CodeIgniter\Autoloader\Autoloader;
use CodeIgniter\Events\Events;
use ReflectionProperty;

/**
 * Registers all Bonfire Module namespaces and app module namespaces
 */
Events::on('pre_system', static function (): void {
    helper('filesystem');
    $map = directory_map(__DIR__ . '/../', 1);
    /** @var Autoloader $autoloader */
    $autoloader = service('autoloader');

    $nonModuleFolders = [
        'Config', 'Core',
    ];

    $namespaces = [];

    foreach ($map as $row) {
        if (substr($row, -1) !== DIRECTORY_SEPARATOR || in_array(trim($row, '/ '), $nonModuleFolders, true)) {
            continue;
        }

        $name = trim($row, DIRECTORY_SEPARATOR);

        $namespaces["Bonfire\\{{$name}}"] = [realpath(__DIR__ . "/../{$name}")];
    }

    // Now define app modules nemespaces
    $appModulesPaths = config('Bonfire')->appModules;

    if (is_array($appModulesPaths) && $appModulesPaths !== []) {
        foreach ($appModulesPaths as $baseName => $path) {
            if (! file_exists($path)) {
                continue;
            }

            $map = directory_map($path, 1);

            foreach ($map as $row) {
                $name = trim($row, DIRECTORY_SEPARATOR);

                $namespaces[$baseName . "\\{{$name}}"] = [realpath($path . "/{$name}")];
            }
        }
    }

    // Insert the namespaces into the psr4 array in the autoloader
    // to ensure that Bonfire's files get loader prior to vendor files
    $rp = new ReflectionProperty($autoloader, 'prefixes');
    $rp->setAccessible(true);

    $prefixes = $rp->getValue($autoloader);
    $keys     = array_keys($prefixes);

    $prefixesStart = array_slice($prefixes, 0, array_search('Tests\\Support', $keys, true) + 1);
    $prefixesEnd   = array_slice($prefixes, array_search('Tests\\Support', $keys, true) + 1);
    $prefixes      = array_merge($prefixesStart, $namespaces, $prefixesEnd);

    $rp->setValue($autoloader, $prefixes);
});
