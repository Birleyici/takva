<?php

namespace App\Services;

use App\DTOs\SiteSettingDTO;
use App\Models\SiteSetting;
use App\Repositories\Interfaces\SiteSettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SiteSettingService
{
    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository
    ) {
    }

    public function get(): SiteSettingDTO
    {
        $setting = $this->getModel();

        return SiteSettingDTO::fromModel($setting);
    }

    public function update(array $data): SiteSettingDTO
    {
        $setting = $this->getModel();

        if (!$setting) {
            throw new InvalidArgumentException('Ayar kaydı bulunamadı.');
        }

        $payload = $this->preparePayload($data, $setting);
        $setting = $this->siteSettingRepository->update($setting, $payload);

        return SiteSettingDTO::fromModel($setting);
    }

    private function preparePayload(array $data, SiteSetting $setting): array
    {
        $payload = [
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_address' => $data['contact_address'] ?? null,
            'contact_map_embed' => $data['contact_map_embed'] ?? null,
            'contact_hero_text' => $data['contact_hero_text'] ?? null,
            'social_twitter' => $data['social_twitter'] ?? null,
            'social_instagram' => $data['social_instagram'] ?? null,
            'social_youtube' => $data['social_youtube'] ?? null,
            'social_facebook' => $data['social_facebook'] ?? null,
            'social_whatsapp' => $data['social_whatsapp'] ?? null,
        ];

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->deleteLogoIfExists($setting->logo_path);
            $payload['logo_path'] = $data['logo']->store('site-settings', 'public');
        } elseif ($this->shouldRemoveLogo($data)) {
            $this->deleteLogoIfExists($setting->logo_path);
            $payload['logo_path'] = null;
        }

        return $payload;
    }

    private function shouldRemoveLogo(array $data): bool
    {
        if (!array_key_exists('remove_logo', $data)) {
            return false;
        }

        return filter_var($data['remove_logo'], FILTER_VALIDATE_BOOLEAN);
    }

    private function deleteLogoIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function getModel(): ?SiteSetting
    {
        return $this->siteSettingRepository->first();
    }
}
