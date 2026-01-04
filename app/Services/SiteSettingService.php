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
        $payload = [];
        $scalarFields = [
            'contact_email',
            'contact_phone',
            'contact_address',
            'contact_map_embed',
            'contact_hero_text',
            'social_twitter',
            'social_instagram',
            'social_youtube',
            'social_facebook',
            'social_whatsapp',
        ];

        foreach ($scalarFields as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->deleteFileIfExists($setting->logo_path);
            $payload['logo_path'] = $data['logo']->store('site-settings', 'public');
        } elseif ($this->shouldRemoveLogo($data)) {
            $this->deleteFileIfExists($setting->logo_path);
            $payload['logo_path'] = null;
        }

        if (isset($data['hero_background']) && $data['hero_background'] instanceof UploadedFile) {
            $this->deleteFileIfExists($setting->hero_background_path);
            $payload['hero_background_path'] = $data['hero_background']->store('site-settings', 'public');
        } elseif ($this->shouldRemoveHeroBackground($data)) {
            $this->deleteFileIfExists($setting->hero_background_path);
            $payload['hero_background_path'] = null;
        }

        $themeSettings = $this->prepareThemeSettings($data, $setting);
        if ($themeSettings !== null) {
            $payload['theme_settings'] = $themeSettings;
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

    private function shouldRemoveHeroBackground(array $data): bool
    {
        if (!array_key_exists('remove_hero_background', $data)) {
            return false;
        }

        return filter_var($data['remove_hero_background'], FILTER_VALIDATE_BOOLEAN);
    }

    private function prepareThemeSettings(array $data, SiteSetting $setting): ?array
    {
        $themeSettings = $this->normalizeThemeSettings($setting->theme_settings ?? []);
        $touched = false;

        if (array_key_exists('header_pattern_opacity', $data)) {
            $themeSettings['header']['opacity'] = $this->normalizeOpacity($data['header_pattern_opacity']);
            $touched = true;
        }

        if (array_key_exists('header_pattern_placement', $data)) {
            $themeSettings['header']['placement'] = $this->normalizePlacement($data['header_pattern_placement']);
            $touched = true;
        }

        if (isset($data['header_pattern']) && $data['header_pattern'] instanceof UploadedFile) {
            $this->deleteFileIfExists($themeSettings['header']['pattern_path'] ?? null);
            $themeSettings['header']['pattern_path'] = $data['header_pattern']->store('site-settings/theme', 'public');
            $touched = true;
        }

        if ($this->shouldRemoveHeaderPattern($data)) {
            $this->deleteFileIfExists($themeSettings['header']['pattern_path'] ?? null);
            $themeSettings['header']['pattern_path'] = null;
            $touched = true;
        }

        if (array_key_exists('footer_pattern_opacity', $data)) {
            $themeSettings['footer']['opacity'] = $this->normalizeOpacity($data['footer_pattern_opacity']);
            $touched = true;
        }

        if (array_key_exists('footer_pattern_placement', $data)) {
            $themeSettings['footer']['placement'] = $this->normalizePlacement($data['footer_pattern_placement']);
            $touched = true;
        }

        if (isset($data['footer_pattern']) && $data['footer_pattern'] instanceof UploadedFile) {
            $this->deleteFileIfExists($themeSettings['footer']['pattern_path'] ?? null);
            $themeSettings['footer']['pattern_path'] = $data['footer_pattern']->store('site-settings/theme', 'public');
            $touched = true;
        }

        if ($this->shouldRemoveFooterPattern($data)) {
            $this->deleteFileIfExists($themeSettings['footer']['pattern_path'] ?? null);
            $themeSettings['footer']['pattern_path'] = null;
            $touched = true;
        }

        if (array_key_exists('home_articles_pattern_opacity', $data)) {
            $themeSettings['home_articles']['opacity'] = $this->normalizeOpacity($data['home_articles_pattern_opacity']);
            $touched = true;
        }

        if (array_key_exists('home_articles_pattern_placement', $data)) {
            $themeSettings['home_articles']['placement'] = $this->normalizePlacement($data['home_articles_pattern_placement']);
            $touched = true;
        }

        if (isset($data['home_articles_pattern']) && $data['home_articles_pattern'] instanceof UploadedFile) {
            $this->deleteFileIfExists($themeSettings['home_articles']['pattern_path'] ?? null);
            $themeSettings['home_articles']['pattern_path'] = $data['home_articles_pattern']->store('site-settings/theme', 'public');
            $touched = true;
        }

        if ($this->shouldRemoveHomeArticlesPattern($data)) {
            $this->deleteFileIfExists($themeSettings['home_articles']['pattern_path'] ?? null);
            $themeSettings['home_articles']['pattern_path'] = null;
            $touched = true;
        }

        if (array_key_exists('home_issues_pattern_opacity', $data)) {
            $themeSettings['home_issues']['opacity'] = $this->normalizeOpacity($data['home_issues_pattern_opacity']);
            $touched = true;
        }

        if (array_key_exists('home_issues_pattern_placement', $data)) {
            $themeSettings['home_issues']['placement'] = $this->normalizePlacement($data['home_issues_pattern_placement']);
            $touched = true;
        }

        if (isset($data['home_issues_pattern']) && $data['home_issues_pattern'] instanceof UploadedFile) {
            $this->deleteFileIfExists($themeSettings['home_issues']['pattern_path'] ?? null);
            $themeSettings['home_issues']['pattern_path'] = $data['home_issues_pattern']->store('site-settings/theme', 'public');
            $touched = true;
        }

        if ($this->shouldRemoveHomeIssuesPattern($data)) {
            $this->deleteFileIfExists($themeSettings['home_issues']['pattern_path'] ?? null);
            $themeSettings['home_issues']['pattern_path'] = null;
            $touched = true;
        }

        if (array_key_exists('home_videos_pattern_opacity', $data)) {
            $themeSettings['home_videos']['opacity'] = $this->normalizeOpacity($data['home_videos_pattern_opacity']);
            $touched = true;
        }

        if (array_key_exists('home_videos_pattern_placement', $data)) {
            $themeSettings['home_videos']['placement'] = $this->normalizePlacement($data['home_videos_pattern_placement']);
            $touched = true;
        }

        if (isset($data['home_videos_pattern']) && $data['home_videos_pattern'] instanceof UploadedFile) {
            $this->deleteFileIfExists($themeSettings['home_videos']['pattern_path'] ?? null);
            $themeSettings['home_videos']['pattern_path'] = $data['home_videos_pattern']->store('site-settings/theme', 'public');
            $touched = true;
        }

        if ($this->shouldRemoveHomeVideosPattern($data)) {
            $this->deleteFileIfExists($themeSettings['home_videos']['pattern_path'] ?? null);
            $themeSettings['home_videos']['pattern_path'] = null;
            $touched = true;
        }

        if (!$touched) {
            return null;
        }

        unset(
            $themeSettings['header']['pattern_url'],
            $themeSettings['footer']['pattern_url'],
            $themeSettings['home_articles']['pattern_url'],
            $themeSettings['home_issues']['pattern_url'],
            $themeSettings['home_videos']['pattern_url']
        );

        return $themeSettings;
    }

    private function normalizeThemeSettings($settings): array
    {
        $normalized = is_array($settings) ? $settings : [];
        $normalized['header'] = $this->normalizeThemeSection($normalized['header'] ?? null);
        $normalized['footer'] = $this->normalizeThemeSection($normalized['footer'] ?? null);
        $normalized['home_articles'] = $this->normalizeThemeSection($normalized['home_articles'] ?? null);
        $normalized['home_issues'] = $this->normalizeThemeSection($normalized['home_issues'] ?? null);
        $normalized['home_videos'] = $this->normalizeThemeSection($normalized['home_videos'] ?? null);

        return $normalized;
    }

    private function normalizeThemeSection($section): array
    {
        $section = is_array($section) ? $section : [];

        return array_merge($section, [
            'pattern_path' => $section['pattern_path'] ?? null,
            'opacity' => $this->normalizeOpacity($section['opacity'] ?? 20),
            'placement' => $this->normalizePlacement($section['placement'] ?? 'repeat'),
        ]);
    }

    private function normalizeOpacity($value): int
    {
        if (!is_numeric($value)) {
            return 20;
        }

        $opacity = (int) round($value);

        return max(0, min(100, $opacity));
    }

    private function normalizePlacement($value): string
    {
        if (!is_string($value)) {
            return 'repeat';
        }

        $value = strtolower(trim($value));

        if ($value === 'fit') {
            $value = 'cover';
        }

        return in_array($value, ['repeat', 'cover', 'contain'], true) ? $value : 'repeat';
    }

    private function shouldRemoveHeaderPattern(array $data): bool
    {
        if (!array_key_exists('remove_header_pattern', $data)) {
            return false;
        }

        return filter_var($data['remove_header_pattern'], FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldRemoveFooterPattern(array $data): bool
    {
        if (!array_key_exists('remove_footer_pattern', $data)) {
            return false;
        }

        return filter_var($data['remove_footer_pattern'], FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldRemoveHomeArticlesPattern(array $data): bool
    {
        if (!array_key_exists('remove_home_articles_pattern', $data)) {
            return false;
        }

        return filter_var($data['remove_home_articles_pattern'], FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldRemoveHomeIssuesPattern(array $data): bool
    {
        if (!array_key_exists('remove_home_issues_pattern', $data)) {
            return false;
        }

        return filter_var($data['remove_home_issues_pattern'], FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldRemoveHomeVideosPattern(array $data): bool
    {
        if (!array_key_exists('remove_home_videos_pattern', $data)) {
            return false;
        }

        return filter_var($data['remove_home_videos_pattern'], FILTER_VALIDATE_BOOLEAN);
    }

    private function deleteFileIfExists(?string $path): void
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
