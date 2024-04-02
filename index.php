<?php

// Sitemap dosyasının oluşturulacağı dosya yolunu belirtin
$sitemap_dosyasi = "sitemap.xml";

// Robots.txt dosyasının oluşturulacağı dosya yolunu belirtin
$robots_txt_dosyasi = "robots.txt";

// Websitenizin ana URL'si
$ana_url = "https://yazilimrehberi.dev";



// ekstra url'ler
// eğer kullanmayacaksanız yorum satırına alın
$eklenmek_istenen_ekstra_urller = ["anasayfa", "urunler", "giris", "kayit", "iletisim"];

// URL adresleri listesi
// url adresleri JSON formatında gönderilmesi gerekiyor!!!
$api_url = "https://json-formatında-gelen-url-ler";

// API URL'sinin var olup olmadığını kontrol et
if (filter_var($api_url, FILTER_VALIDATE_URL) && @file_get_contents($api_url)) {
    // API'den verileri al
    $response = file_get_contents($api_url);
    $veriler = json_decode($response, true);
    
    // Verileri işle...
} else {
    echo "Hata: API URL'sine erişilemiyor veya yanlış belirtilmiş.";
    exit();
}


// Sitemap dosyasını oluştur
$sitemap_icerik = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemap_icerik .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Robots.txt dosyasını oluştur
$robots_icerik = "User-agent: *\n";

// Ana URL'yi sitemap dosyasına ekle
$sitemap_icerik .= "\t<url>\n";
$sitemap_icerik .= "\t\t<loc>{$ana_url}</loc>\n";
$sitemap_icerik .= "\t\t<lastmod>" . date("Y-m-d") . "</lastmod>\n";
$sitemap_icerik .= "\t\t<changefreq>" . "monthly" . "</changefreq>\n";
$sitemap_icerik .= "\t\t<priority>" . "1" . "</priority>\n";
$sitemap_icerik .= "\t</url>\n";

// Verilerden URL'lerinizi sitemap dosyasına ve robots.txt dosyasına ekleyin
if (isset($eklenmek_istenen_ekstra_urller) || !is_null($eklenmek_istenen_ekstra_urller)) {
    foreach ($eklenmek_istenen_ekstra_urller as $veri) {
        $veri_url = "{$ana_url}/{$veri}";
        // Sitemap dosyasına ekle
        $sitemap_icerik .= "\t<url>\n";
        $sitemap_icerik .= "\t\t<loc>{$veri_url}</loc>\n";
        $sitemap_icerik .= "\t\t<lastmod>" . date("Y-m-d") . "</lastmod>\n";
        $sitemap_icerik .= "\t\t<changefreq>" . "monthly" . "</changefreq>\n";
        $sitemap_icerik .= "\t\t<priority>" . "0.9" . "</priority>\n";
        $sitemap_icerik .= "\t</url>\n";
    }
}

if (isset($veriler) && !is_null($veriler)) {
    foreach ($veriler as $veri) {
        $veri_url = "{$ana_url}/{$veri}";

        // URL'nin durum kodunu kontrol et
        $url_durum_kodu = get_http_response_code($veri_url);

        // Eğer URL 200 durum kodu alıyorsa sitemap dosyasına ekle
        if ($url_durum_kodu == 200) {
            // Sitemap dosyasına ekle
            $sitemap_icerik .= "\t<url>\n";
            $sitemap_icerik .= "\t\t<loc>{$veri_url}</loc>\n";
            $sitemap_icerik .= "\t\t<lastmod>" . date("Y-m-d") . "</lastmod>\n";
            $sitemap_icerik .= "\t\t<changefreq>" . "monthly" . "</changefreq>\n";
            $sitemap_icerik .= "\t\t<priority>" . "0.5" . "</priority>\n";
            $sitemap_icerik .= "\t</url>\n";
        }
    }
}

$sitemap_icerik .= '</urlset>';

// Robots.txt dosyasını tamamla
$robots_icerik .= "Allow: /\n";
$robots_icerik .= "Sitemap: {$ana_url}/sitemap.xml\n";

// Dosyaları oluştur
file_put_contents($sitemap_dosyasi, $sitemap_icerik);
file_put_contents($robots_txt_dosyasi, $robots_icerik);

echo "Sitemap ve Robots.txt başarıyla oluşturuldu.";

// URL'nin durum kodunu almak için işlev
function get_http_response_code($url)
{
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

?>
