-- #1
SELECT DISTINCT status
FROM tasks
ORDER BY CONCAT(status) ASC;

+-------------+
| status      |
+-------------+
| completed   |
| deleted     |
| in_progress |
| new         |
+-------------+

-- #2
SELECT p.name, COUNT(t.name) cnt
FROM projects p
LEFT JOIN tasks t ON t.project_id = p.id
GROUP BY p.name
ORDER BY cnt DESC;

+-------------+-----+
| name        | cnt |
+-------------+-----+
| B project   |  17 |
| N1 project  |  14 |
| Garage      |   7 |
| N project   |   5 |
| A project   |   4 |
| Project a 1 |   2 |
| C project   |   0 |
| L project   |   0 |
| Z project   |   0 |
| Project a 2 |   0 |
| M project   |   0 |
+-------------+-----+

-- #3
SELECT p.name, COUNT(t.name) cnt
FROM projects p
LEFT JOIN tasks t ON t.project_id = p.id
GROUP BY p.name
ORDER BY p.name ASC;

+-------------+-----+
| name        | cnt |
+-------------+-----+
| A project   |   4 |
| B project   |  17 |
| C project   |   0 |
| Garage      |   7 |
| L project   |   0 |
| M project   |   0 |
| N project   |   5 |
| N1 project  |  14 |
| Project a 1 |   2 |
| Project a 2 |   0 |
| Z project   |   0 |
+-------------+-----+

-- #4
SELECT p.name project_name, t.name task_name, t.status task_status
FROM projects p
INNER JOIN tasks t ON t.project_id = p.id
WHERE SUBSTR(p.name, 1, 1) = 'N';

+--------------+---------------------------+-------------+
| project_name | task_name                 | task_status |
+--------------+---------------------------+-------------+
| N project    | Task N-1                  | completed   |
| N project    | Task N-2                  | completed   |
| N project    | Task N-3                  | new         |
| N project    | Task N-4                  | in_progress |
| N project    | Task N-4                  | completed   |
| N1 project   | Task N1-1                 | completed   |
| N1 project   | project N1 - completed 1  | completed   |
| N1 project   | project N1 - completed 2  | completed   |
| N1 project   | project N1 - completed 3  | completed   |
| N1 project   | project N1 - completed 4  | completed   |
| N1 project   | project N1 - completed 5  | completed   |
| N1 project   | project N1 - completed 6  | completed   |
| N1 project   | project N1 - completed 7  | completed   |
| N1 project   | project N1 - completed 8  | completed   |
| N1 project   | project N1 - completed 9  | completed   |
| N1 project   | project N1 - completed 10 | completed   |
| N1 project   | project N1 - completed 11 | completed   |
| N1 project   | project N1 - completed 12 | completed   |
| N1 project   | project N1 - completed 13 | completed   |
+--------------+---------------------------+-------------+

-- #5
SELECT p.name, COUNT(t.name) cnt
FROM projects p
LEFT JOIN tasks t ON t.project_id = p.id
WHERE p.name REGEXP '^[^a]+a[^a]+$'
GROUP BY p.name;

+-------------+-----+
| name        | cnt |
+-------------+-----+
| Project a 1 |   2 |
| Project a 2 |   0 |
+-------------+-----+

-- #6
SELECT t.name, COUNT(*) cnt
FROM tasks t
GROUP BY t.name
HAVING cnt > 1
ORDER BY t.name ASC;

+-----------------+-----+
| name            | cnt |
+-----------------+-----+
| Garage - task 1 |   4 |
| Garage - task 4 |   2 |
| Task B-2        |   4 |
| Task N-4        |   2 |
+-----------------+-----+

-- #7
SELECT t.name, t.status, count(t.name) cnt
FROM projects p
INNER JOIN tasks t ON t.project_id = p.id
WHERE p.name = 'Garage'
GROUP BY t.name, t.status
HAVING cnt > 1
ORDER BY cnt ASC;

+-----------------+--------+-----+
| name            | status | cnt |
+-----------------+--------+-----+
| Garage - task 4 | new    |   2 |
| Garage - task 1 | new    |   3 |
+-----------------+--------+-----+

-- #8
SELECT p.name, count(*) cnt
FROM projects p
INNER JOIN tasks t ON t.project_id = p.id AND t.status = "completed"
GROUP BY p.name
HAVING cnt > 10
ORDER BY p.id ASC;

+------------+-----+
| name       | cnt |
+------------+-----+
| B project  |  12 |
| N1 project |  14 |
+------------+-----+

-- Tested in MySQL
mysql> select version();
+-------------------------+
| version()               |
+-------------------------+
| 5.5.47-0ubuntu0.12.04.1 |
+-------------------------+

-- Tables dump
CREATE TABLE `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` enum('new','in_progress','completed','deleted') DEFAULT NULL,
  `project_id` int(11) unsigned,
  PRIMARY KEY (`id`),
  KEY `frg` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into projects (name) values ('A project'), ('B project'), ('C project'), ('N project'), ('N1 project'), ('M project'), ('L project'), ('Z project'), ('Project a 1'), ('Project a 2');
insert into tasks (name, status, project_id) values ('Task A-1', 'new', 1), ('Task A-2', 'completed', 1), ('Task A-3', 'completed', 1), ('Task A-4', 'new', 1),
('Task B-1', 'new', 2), ('Task B-2', 'deleted', 2), ('Task B-3', 'completed', 2),
('Task N-1', 'completed', 4), ('Task N-2', 'completed', 4), ('Task N-3', 'new', 4), ('Task N-4', 'in_progress', 4), ('Task N1-1', 'completed', 5),
('Task without project', 'new', null), ('task 1 - Project a 1', 'new', 9), ('task 2 - Project a 1', 'new', 9), ('Task B-2', 'new', 2), ('Task B-2', 'new', 2), ('Task B-2', 'new', 2), ('Task N-4', 'completed', 4);

insert into projects (name) values ('Garage');
insert into tasks (name, status, project_id) values ('Garage - task 1', 'new', 11), ('Garage - task 2', 'new', 11), ('Garage - task 1', 'new', 11), ('Garage - task 1', 'completed', 11), ('Garage - task 1', 'new', 11), ('Garage - task 4', 'new', 11), ('Garage - task 4', 'new', 11);
insert into tasks (name, status, project_id) values ('project B - completed 1', 'completed', 2),
    ('project B - completed 2', 'completed', 2),
    ('project B - completed 3', 'completed', 2),
    ('project B - completed 4', 'completed', 2),
    ('project B - completed 5', 'completed', 2),
    ('project B - completed 6', 'completed', 2),
    ('project B - completed 7', 'completed', 2),
    ('project B - completed 8', 'completed', 2),
    ('project B - completed 9', 'completed', 2),
    ('project B - completed 10', 'completed', 2),
    ('project B - completed 11', 'completed', 2),
    ('project N1 - completed 1', 'completed', 5),
    ('project N1 - completed 2', 'completed', 5),
    ('project N1 - completed 3', 'completed', 5),
    ('project N1 - completed 4', 'completed', 5),
    ('project N1 - completed 5', 'completed', 5),
    ('project N1 - completed 6', 'completed', 5),
    ('project N1 - completed 7', 'completed', 5),
    ('project N1 - completed 8', 'completed', 5),
    ('project N1 - completed 9', 'completed', 5),
    ('project N1 - completed 10', 'completed', 5),
    ('project N1 - completed 11', 'completed', 5),
    ('project N1 - completed 12', 'completed', 5),
    ('project N1 - completed 13', 'completed', 5);
