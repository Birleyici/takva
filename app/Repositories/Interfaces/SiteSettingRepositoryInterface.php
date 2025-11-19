<?php

namespace App\Repositories\Interfaces;

use App\Models\SiteSetting;

interface SiteSettingRepositoryInterface
{
    public function first(): ?SiteSetting;

    public function update(SiteSetting $setting, array $data): SiteSetting;
}
