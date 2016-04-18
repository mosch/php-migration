<?php
namespace Migration;

class VersionTransducer
{
    private $migrations = [];
    private $versionProvider;

    public function __construct(VersionProviderInterface $versionProvider)
    {
        $this->versionProvider = $versionProvider;
    }

    public function addMigration(MigrationInterface $migration)
    {
        if (false === $this->isVersionNameValid($migration->getVersionName())) {
            throw new \Exception(sprintf('Version name %s of migration %s is invalid', $migration->getVersionName(), get_class($migration)));
        }

        $this->migrations[$migration->getVersionName()] = $migration;
        ksort($this->migrations, SORT_NATURAL);
    }

    public function migrateUp()
    {
        $currentVersion = $this->versionProvider->getCurrentVersion();
        $executeMigrations = array_slice($this->migrations, array_search($currentVersion, array_keys($this->migrations))+1);

        foreach ($executeMigrations as $executeMigration) {
            /** @var Migration $executeMigration */
            $executeMigration->migrate();
            $this->versionProvider->setCurrentVersion($executeMigration->getVersionName());
        }
    }

    public static function createVersionName()
    {
        return date('Y-m-d-His');
    }

    private function isVersionNameValid($version)
    {
        return preg_match('/\d{4}-[0-1]\d-[0-3]\d-\d{6}$/', $version) > 0;
    }
}