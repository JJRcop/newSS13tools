/* Saving for later*/

SELECT count(DISTINCT ss13feedback.round_id) AS rounds,
concat(MONTH(ss13feedback.time),'-',YEAR(ss13feedback.time)) AS `date`,
SUM(TIMESTAMPDIFF(MINUTE,STR_TO_DATE(ss13feedback.details,'%a %b %d %H:%i:%s %Y'),STR_TO_DATE(end.details,'%a %b %d %H:%i:%s %Y'))) / count(DISTINCT ss13feedback.round_id) AS `avg_duration`,
MIN(ss13feedback.round_id) AS firstround,
MAX(ss13feedback.round_id) AS lastround
FROM ss13feedback
LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
WHERE ss13feedback.time BETWEEN '2011-01-01' AND NOW()
AND ss13feedback.var_name='round_start'
GROUP BY YEAR(ss13feedback.time), MONTH(ss13feedback.time) ASC;

SELECT count(ss13feedback.details) AS count,
ss13feedback.details AS result,
mode.details AS game_mode
  FROM ss13feedback
  LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
  WHERE ss13feedback.var_name='round_end_result'
  AND ss13feedback.time BETWEEN '2011-01-01' AND NOW()
  GROUP BY result
  ORDER BY game_mode;