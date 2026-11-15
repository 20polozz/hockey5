API endpoints (MySQL)

1) Copy api/config.sample.php to api/config.php and set credentials.
2) Import hoquei5-db.sql in phpMyAdmin (it creates DB 'hoquei5-db').

Endpoints:
- /api/okliga_teams.php                         -> [{name, city}]
- /api/hockeygrid_teams.php?league=espana       -> ["Barça", ...]
- /api/hockeygrid_teams.php?league=portugal     -> [...]
- /api/hockeygrid_teams.php?league=internacional-> union
- /api/hockeygrid_players.php?league=espana|portugal|internacional
- /api/teamlogos.php?league=espana|portugal|internacional
