<?php

namespace App\Repositories;

use App\Models\SiteSetting;
use App\Repositories\Interfaces\SiteSettingRepositoryInterface;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    public function first(): ?SiteSetting
    {
        return SiteSetting::query()->first();
    }

    public function update(SiteSetting $setting, array $data): SiteSetting
    {
        $setting->fill($data);
        $setting->save();

        return $setting;
    }
}
