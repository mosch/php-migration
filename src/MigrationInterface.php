<?php
namespace Migration;

interface MigrationInterface
{
    public function getVersionName();
    public function migrate();
}
    