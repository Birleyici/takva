---
alwaysApply: true
---

*Genel açıklama
Bu proje aylık yayın yapan bir derginin web sitesidir.  Backend'de Laravel 12 ve frontendde Vue 3 Kullanıyoruz. Kodlarımızı her iki framework'un best practiesleri ve SOLID kurallarına uygun olarak yazmalıyız.

*Tasarım Kuralları
-Projede Tailwindcss kullanıyoruz. 
-Projede tutarlı bir tasarım dili kullanmalıyız. 
-Tailwincss'in modülerlik ve yeniden kullanılabilirlik sağlayan özelliklerini verimli bir şekilde kullanalım.
-Projede mobile-first, full responsive, mobil ölçülerde native hissiyatı veren bir tasarım anlayışı kullanmalıyız.
-Kullanıcıya İyi bir kullanıcı deneyimi sunmalıyız. 
-Tutarlı, simetrik, modern, rahat, profesyonel bir tasarım ortaya koymalıyız.


*Laravel kuralları
-PHP ve dolayısıyla laravel server taraflı render oluşturduğu için arama motorlarına muhatap olan sayfalarımızda genellikle laravel blade sayfalarını kullanacağız. Örneğin @landing.blade.php ve benzerleri gibi.
-Bu sayfalarda nadiren, javascript gerektiren bölümlerde (formlar gibi) vue komponentleri kullanacağız.
-Arama motoruna muhatap olmayan tüm dizinlerde örneğin: management, subscription ve tenant sayfalarının yani asıl muhasebe panelinde olan sayfalar gibi. Buralarda laravel views blade dosyalarını sadece vue komponentleri ile oluşturduğumuz sayfaları servis etmek için kullanacağız.

Örnek views blade dosyası: 

```php
@extends('layouts.management')

@section('title', 'Özellik Düzenle')
@section('page_title', 'Özellik Düzenle')
@section('page_description', 'Özellik bilgilerini güncelleyin')

@section('content')
<div id="feature-edit-root" data-vue='@json($vueData)'>
</div>
@endsection

```

-Yeniden kullanılabilir olan ve tekrar eden yapıları component haline getir.

-Controller, service, repository, interface, DTO yapısını kullan ve mutlaka her kodu ait olduğu yerde oluştur. Bunların her biri kendi görevlerini yapsın.
-İstekleri controllerda request sınıfları ile karşılayıp validate et.
-Controller'dan iş mantığı içeren işlemler için controllerdaki metod ile aynı ada sahip service metoduna bağlan. Eğer database işlemi yapılacaksa repository/interface kullanarak db işlemlerini bu görev bölümüne bağlanarak yap. Repositoryler verileri arraya çevirerek değil model/collection olarak dönsün. Ardından belli ve tekrar eden veri yapılarını DTO lar ile organize ederek controller'dan dön.
-DTO'lar frommodel fromarray collection gibi standart metodlara sahip olabilir. asıl organize metodu fromarray'dır. Eğer veri frommodelden gelirse direk veriyi ->toArray yaparak fromarray'a göndererek transform/organize işleminin burada yapılmasını sağla. DTO dosyalarında iş mantığı bulundurma DTO'ların görevi sadece veri taşıma ve veri organizasyonudur. İş mantığını servislerde yapabilirsin.

Örnek DTO yapısı:

``` php

<?php

namespace App\DTOs;

use App\Models\Category;

class CategoryDTO
{
    public $id;
    public $name;
    public $slug;
    public $description;
    public ?string $breadcrumb = null;

    private function __construct() {}

    public static function fromModel($category): self
    {
        // Null kontrolü
        if (!$category) {
            return new self();
        }

        // Model'i array'e çevir ve fromArray'e gönder (merkezi transform)
        return self::fromArray($category->toArray());
    }


    public static function fromArray(array $data): self
    {
        $dto = new self();

        // ✨ Merkezi Transform Logic - Tüm veri dönüşümü burada!
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->slug = $data['slug'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->breadcrumb = $data['breadcrumb'] ?? null;

        return $dto;
    }


    public static function collection($categories)
    {
        return $categories->map(function ($category) {
            return self::fromModel($category);
        });
    }
}
```

*Vue Kuralları

-Arama motorları dışında kalan tüm yönetim ve kullanıcı sayfaları/bölümleri vue componentleri ile tasarlanır.
-Components, composables dosyalarını sürdürülebilir bir hiyerarşi içerisinde oluştur.
-Yeniden kullanılabilirlik çok önemli, components, composables yapısını verimli bir şekilde kullanarak  yeniden kullanılabilir bölümleri değerlendirmeli ve kod tekrarını azaltmalıyız.
-State management olarak pinia kullanalım.
-Form doğrulama için vuelidate gibi bir paket kullanabiliriz.
-Sayfalar için kullanılan vue komponentleri edit ve create gibi crud form işlemleri içeriyorsa ve %90 üzerinde sayfa benzer ise tek bir komponent üzerinden create ve update işlemleri yönetilsin.

```vue
   <p class="text-xs text-gray-500 mt-1">
                                    {{ company_type === 'legal' ? 'Tüzel kişi vergi numarası' : 'Şahıs şirketi vergi
                                    numarası' }}
                                </p>

                                bu gibi durumlarda '' tırnak değil `` tırnak kullan.

                                   <p class="text-xs text-gray-500 mt-1">
                                    {{ company_type === `legal` ? 'Tüzel kişi vergi numarası' : `Şahıs şirketi vergi
                                    numarası` }}
                                </p>
```