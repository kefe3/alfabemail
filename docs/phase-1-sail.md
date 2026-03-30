# Faz 1 - Docker/Sail Geçiş Hazırlığı

Bu fazda amaç, mevcut `alfabemail` klasörünü Laravel'e taşımadan önce Docker altyapısını Mailcow ile aynı ağda çalışacak şekilde hazırlamaktır.

## Yapılanlar

- `docker-compose.yml` eklendi.
  - `app` servisi (PHP container)
  - `mysql` servisi
  - `redis` servisi
- `mailcow_shared` adlı **external network** tanımlandı.
  - Varsayılan değer: `mailcowdockerized_mailcow-network`
  - `.env.sail.example` içindeki `MAILCOW_DOCKER_NETWORK` ile değiştirilebilir.
- `docker/php/Dockerfile` eklendi.
- `.env.sail.example` eklendi.

## Not

- Bu fazda internet/policy kısıtı nedeniyle Laravel 11 paketleri `composer create-project` ile indirilemedi (packagist 403).
- Sonraki fazda (onay sonrası) Laravel iskeleti ve Filament/Spatie entegrasyonu kurulacaktır.

## Çalıştırma

```bash
cp .env.sail.example .env
# Gerekirse MAILCOW_DOCKER_NETWORK değerini düzenle

docker compose up -d --build
```

Network kontrolü:

```bash
docker network ls | grep mailcow
```
