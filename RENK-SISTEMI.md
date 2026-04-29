# Sıcak Toprak Tonları Renk Sistemi

## 4 Ana Renk Paleti - Seçenek 2

Sitenin tamamında kullanılan 4 doğal ve uyumlu İslami renk:

### 1. Primary (Koyu Zeytin Yeşili) - Mübarek Zeytin Ağacı
- **Ana Kullanım**: Logo, ana butonlar, linkler, vurgular
- **Tailwind Sınıfları**: `primary-50` ile `primary-900`
- **Ana Renk**: `primary-500` (#558B2F)
- **CSS Değişkeni**: `--color-primary`
- **Anlamı**: Zeytin ağacı (Kur'an'da geçen mübarek ağaç), doğa, bereket

### 2. Secondary (Koyu Terracotta) - Toprak ve Doğallık
- **Ana Kullanım**: Başlıklar, metin renkleri, önemli vurgular
- **Tailwind Sınıfları**: `secondary-50` ile `secondary-900`
- **Ana Renk**: `secondary-500` (#8B4513)
- **CSS Değişkeni**: `--color-secondary`
- **Anlamı**: Toprak, doğallık, sıcaklık

### 3. Accent (Koyu Altın) - Güneş ve Aydınlık
- **Ana Kullanım**: CTA butonları, özel vurgular, dekoratif elementler
- **Tailwind Sınıfları**: `accent-50` ile `accent-900`
- **Ana Renk**: `accent-500` (#F57C00)
- **CSS Değişkeni**: `--color-accent`
- **Anlamı**: Güneş, aydınlık, enerji

### 4. Neutral (Koyu Bej) - Çöl ve Tevazu
- **Ana Kullanım**: Arka planlar, kenarlıklar, ikincil metinler
- **Tailwind Sınıfları**: `neutral-50` ile `neutral-900`
- **Ana Renk**: `neutral-500` (#795548)
- **CSS Değişkeni**: `--color-neutral`
- **Anlamı**: Çöl, tevazu, sadelik

## Özel Component Sınıfları

### Butonlar
```css
.btn-primary    /* Ana yeşil buton */
.btn-secondary  /* Kahverengi buton */
.btn-accent     /* Altın sarısı buton */
```

### Kartlar
```css
.card           /* Standart kart tasarımı */
```

### Metin Renkleri
```css
.text-primary   /* Ana yeşil metin */
.text-secondary /* Kahverengi metin */
.text-accent    /* Altın sarısı metin */
```


.
## Renkleri Değiştirme

### 1. Tailwind Config'de Değiştirme
`tailwind.config.js` dosyasında renk değerlerini güncelleyin:

```javascript
colors: {
  primary: {
    500: '#YENİ_RENK_KODU',  // Ana rengi değiştir
    // Diğer tonları da güncelle
  }
}
```

### 2. CSS Değişkenlerinde Değiştirme
`resources/css/app.css` dosyasında CSS değişkenlerini güncelleyin:

```css
:root {
  --color-primary: #YENİ_RENK_KODU;
}
```

## Kullanım Örnekleri

### HTML/Vue Template'de
```html
<!-- Tailwind sınıfları ile -->
<div class="bg-primary-500 text-white">Ana renk arka plan</div>
<button class="btn-primary">Ana buton</button>

<!-- Component sınıfları ile -->
<div class="card">Standart kart</div>
<p class="text-primary">Ana renk metin</p>
```

### CSS'de
```css
.custom-element {
  background-color: var(--color-primary);
  border-color: var(--color-accent);
}
```

## Avantajları

1. **Merkezi Yönetim**: Tüm renkler tek yerden kontrol edilir
2. **Kolay Değişiklik**: Renk paletini değiştirmek için sadece config dosyalarını güncelle
3. **Tutarlılık**: Tüm sitede aynı renk sistemi kullanılır
4. **Performans**: Önceden tanımlı component sınıfları ile hızlı geliştirme
5. **Bakım Kolaylığı**: Inline renkler yerine semantic isimler kullanılır
6. **Düz Renkler**: Gradyan yerine düz renkler kullanılarak daha temiz görünüm

## Renk Seçim Rehberi

- **Primary**: Ana marka rengi, en önemli elementler için
- **Secondary**: İkincil önem, başlıklar ve metin için  
- **Accent**: Dikkat çekmek istediğiniz elementler için
- **Neutral**: Arka planlar ve yardımcı elementler için