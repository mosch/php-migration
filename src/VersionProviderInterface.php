<?php
namespace Migration;

interface VersionProviderInterface
{
    public function getCurrentVersion();
    public function setCurrentVersion($version);
}
