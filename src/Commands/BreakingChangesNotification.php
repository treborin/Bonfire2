<?php

namespace Bonfire\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BreakingChangesNotification extends BaseCommand
{
    protected $group       = 'Bonfire';
    protected $name        = 'notify:breaking-changes';
    protected $description = 'Notifies about breaking changes after composer update.';

    public function run(array $params)
    {
        $lastUpdateFile = BFPATH . 'last-update.txt';
        $changelogFile = BFPATH . 'docs/intro/changelog.md';

        // Read the last update date
        $lastUpdateDate = $this->getLastUpdateDate($lastUpdateFile);

        // Read the changelog file
        $breakingChangeDate = $this->getLatestBreakingChangeDate($changelogFile);

        if ($breakingChangeDate && (!$lastUpdateDate || $lastUpdateDate < $breakingChangeDate)) {
            CLI::write('Warning: Breaking changes detected. Please read the changelog for more information.', 'yellow');
            CLI::write('Breaking change date: ' . $breakingChangeDate, 'yellow');

            // Update the last update date
            file_put_contents($lastUpdateFile, $breakingChangeDate);
        } else {
            CLI::write('No new breaking changes detected.', 'green');
        }
    }

    private function getLastUpdateDate(string $filePath): ?string
    {
        if (file_exists($filePath)) {
            return trim(file_get_contents($filePath));
        }

        return null;
    }

    private function getLatestBreakingChangeDate(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $file = fopen($filePath, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                if (strpos($line, '(breaking change)') !== false) {
                    preg_match('/## (\d{1,2} \w+ \d{4})/', $line, $matches);
                    if (isset($matches[1])) {
                        fclose($file);
                        return date('Y-m-d', strtotime($matches[1]));
                    }
                }
            }
            fclose($file);
        }

        return null;
    }
}