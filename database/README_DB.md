# Hoquei-5 — versió amb MySQL (phpMyAdmin)

## 1) Importa la base de dades

1. Obre **phpMyAdmin**
2. **Importar** → selecciona el fitxer `hoquei5-db.sql`
3. Executa la importació

Això crea la BBDD `hoquei5-db` amb taules i dades.

## 2) Configura la connexió (PHP)

A la carpeta `api/`:

- Copia `api/config.sample.php` a `api/config.php`
- Edita les credencials segons el teu servidor:

```php
return [
  'host' => 'localhost',
  'dbname' => 'hoquei5-db',
  'user' => 'root',
  'pass' => '',
  'charset' => 'utf8mb4',
];
```

Alternativament, pots usar variables d'entorn:
- `HOQUEI_DB_HOST`
- `HOQUEI_DB_NAME`
- `HOQUEI_DB_USER`
- `HOQUEI_DB_PASS`

## 3) Estructura API

- `api/okliga_teams.php` → equips OK Lliga per a la home (strip)
- `api/hockeygrid_teams.php?league=espana|portugal|internacional` → llista d'equips (strings)
- `api/hockeygrid_players.php?league=espana|portugal|internacional` → jugadors
- `api/teamlogos.php?league=espana|portugal|internacional` → map {teamName: logoUrl}

## 4) Com provar-ho local

Si tens PHP instal·lat:

```bash
cd hoquei-5
php -S localhost:8000
```

Després obre:
- `http://localhost:8000/index.php`
- `http://localhost:8000/hockeygrid/index.php?league=oklliga`

