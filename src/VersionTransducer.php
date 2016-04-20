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

    /**
     * @return integer
     */
    public function migrateUp()
    {
        $executeMigrations = $this->getOpenMigrations();

        foreach ($executeMigrations as $migration) {
            $migration->migrate();
            $this->versionProvider->addVersion($migration->getVersionName());
        }

        return count($migration);
    }

    /**
     * @return MigrationInterface[]
     */
    public function getOpenMigrations()
    {
        return array_filter($this->migrations, function(MigrationInterface $migration) {
            return $this->versionProvider->hasVersion($migration->getVersionName()) === false;
        });
    }

    /**
     * @return string
     */
    public static function createVersionName()
    {
        return date('Y-m-d-His');
    }

    /**
     * @param $version
     * @return bool
     */
    private function isVersionNameValid($version)
    {
        return preg_match('/\d{4}-[0-1]\d-[0-3]\d-\d{6}$/', $version) > 0;
    }
}
