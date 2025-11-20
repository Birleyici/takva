<?php

namespace App\DTOs;

use App\Models\SiteSetting;

class SiteSettingDTO
{
    public ?int $id = null;
    public ?string $contact_email = null;
    public ?string $contact_phone = null;
    public ?string $contact_address = null;
    public ?string $contact_map_embed = null;
    public ?string $contact_hero_text = null;
    public ?string $logo_path = null;
    public ?string $logo_url = null;
    public ?string $social_twitter = null;
    public ?string $social_instagram = null;
    public ?string $social_youtube = null;
    public ?string $social_facebook = null;
    public ?string $social_whatsapp = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private function __construct()
    {
    }

    public static function fromModel(?SiteSetting $setting): self
    {
        if (!$setting) {
            return new self();
        }

        return self::fromArray($setting->toArray());
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->contact_email = $data['contact_email'] ?? null;
        $dto->contact_phone = $data['contact_phone'] ?? null;
        $dto->contact_address = $data['contact_address'] ?? null;
        $dto->contact_map_embed = $data['contact_map_embed'] ?? null;
        $dto->contact_hero_text = $data['contact_hero_text'] ?? null;
        $dto->logo_path = $data['logo_path'] ?? null;
        $dto->logo_url = $data['logo_url'] ?? null;
        $dto->social_twitter = $data['social_twitter'] ?? null;
        $dto->social_instagram = $data['social_instagram'] ?? null;
        $dto->social_youtube = $data['social_youtube'] ?? null;
        $dto->social_facebook = $data['social_facebook'] ?? null;
        $dto->social_whatsapp = $data['social_whatsapp'] ?? null;
        $dto->created_at = $data['created_at'] ?? null;
        $dto->updated_at = $data['updated_at'] ?? null;

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'contact_address' => $this->contact_address,
            'contact_map_embed' => $this->contact_map_embed,
            'contact_hero_text' => $this->contact_hero_text,
            'logo_path' => $this->logo_path,
            'logo_url' => $this->logo_url,
            'social_twitter' => $this->social_twitter,
            'social_instagram' => $this->social_instagram,
            'social_youtube' => $this->social_youtube,
            'social_facebook' => $this->social_facebook,
            'social_whatsapp' => $this->social_whatsapp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
