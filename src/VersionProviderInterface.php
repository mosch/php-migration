<?php
namespace Migration;

interface VersionProviderInterface
{
    public function hasVersion($version);
    public function addVersion($version);
}
