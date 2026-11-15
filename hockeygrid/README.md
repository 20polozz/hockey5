# HOQUEI5 GRID (versió amb MySQL)

Aquesta versió deixa de carregar dades des de JSON estàtics i passa a carregar-ho tot via **API PHP + MySQL**, mantenint la mateixa funcionalitat del joc.

## 1) Importar la base de dades
1. Obre **phpMyAdmin**
2. **Importar**
3. Selecciona el fitxer `hockey5-gio-db.sql`
4. Executa

> La base de dades es crea com: `hockey5-gio-db`

## 2) Configurar credencials de MySQL
Edita:
- `config/db.php`

...o defineix variables d'entorn (recomanat):
- `HG_DB_HOST`
- `HG_DB_PORT`
- `HG_DB_NAME`
- `HG_DB_USER`
- `HG_DB_PASS`

## 3) Provar en local
Amb PHP built-in server:
```bash
php -S localhost:8000
```
I obre:
- `http://localhost:8000/hockeygrid/index.php`

## Endpoints API
- `GET /hockeygrid/api/allowed_teams.php?league=oklliga`
- `GET /hockeygrid/api/players.php?leagues=oklliga,placard`
- `GET /hockeygrid/api/teamlogos.php`
