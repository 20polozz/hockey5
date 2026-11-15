SELECT
  p.id,
  p.name,
  p.full_name,
  p.current_team_name
FROM players p
JOIN (
  -- Equips previs (per nom normalitzat)
  SELECT
    ppt.player_id,
    ppt.normalized_team_name AS team_norm
  FROM player_previous_teams ppt

  UNION ALL

  -- Equip actual (per id -> teams.normalized_name)
  SELECT
    p2.id AS player_id,
    t.normalized_name AS team_norm
  FROM players p2
  JOIN teams t ON t.id = p2.current_team_id
) x ON x.player_id = p.id
WHERE x.team_norm IN ('igualada', 'sant just')
GROUP BY p.id, p.name, p.full_name, p.current_team_name
HAVING COUNT(DISTINCT x.team_norm) = 2
ORDER BY p.name;
