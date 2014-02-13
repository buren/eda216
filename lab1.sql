-- a
SELECT `Students`.firstName, `Students`.lastName  FROM Students;
-- b
SELECT `Students`.firstName, `Students`.lastName FROM Students ORDER BY lastName;
SELECT `Students`.firstName, `Students`.lastName FROM Students ORDER BY firstName;
-- c
SELECT * FROM Students WHERE `Students`.pNbr LIKE '85%';
-- d
SELECT * FROM Students WHERE MOD( SUBSTRING(`Students`.pNbr, 10, 1), 2) = 0;
-- e
SELECT COUNT(*) FROM Students;
-- f
SELECT * FROM Courses WHERE `Courses`.courseCode LIKE 'FMA%';
-- g
SELECT * FROM Courses WHERE `Courses`.credits > 7.5;
-- h
SELECT level, COUNT(*) FROM Courses GROUP BY `Courses`.level;
-- i
SELECT `TakenCourses`.courseCode FROM TakenCourses
INNER JOIN Students ON `Students`.pNbr = `TakenCourses`.pNbr
WHERE `Students`.pNbr = '910101-1234';  -- WHERE `Students`.firstName = 'Eva' and `Students`.lastName = 'Alm';
-- j
SELECT `Courses`.courseCode, `Courses`.courseName, `Courses`.credits FROM Courses
INNER JOIN TakenCourses  ON `TakenCourses`.courseCode = `Courses`.courseCode
INNER JOIN Students ON `Students`.pNbr = `TakenCourses`.pNbr
WHERE `Students`.pNbr = '910101-1234';  -- WHERE `Students`.firstName = 'Eva' and `Students`.lastName = 'Alm';
-- k
SELECT SUM(`Courses`.credits) FROM Courses
INNER JOIN TakenCourses  ON `TakenCourses`.courseCode = `Courses`.courseCode
INNER JOIN Students ON `Students`.pNbr = `TakenCourses`.pNbr
WHERE `Students`.pNbr = '910101-1234';  -- WHERE `Students`.firstName = 'Eva' and `Students`.lastName = 'Alm';
-- l
SELECT AVG(`TakenCourses`.grade) FROM TakenCourses
INNER JOIN Students ON `Students`.pNbr = `TakenCourses`.pNbr
WHERE `Students`.pNbr = '910101-1234';  -- WHERE `Students`.firstName = 'Eva' and `Students`.lastName = 'Alm';
-- m SEE i-l with: WHERE `Students`.firstName = 'Eva' and `Students`.lastName = 'Alm';
-- n
SELECT * FROM Students
LEFT OUTER JOIN TakenCourses ON `TakenCourses`.pNbr = `Students`.pNbr
WHERE `TakenCourses`.courseCode IS NULL;
-- o
CREATE VIEW student_grade_average AS
  SELECT *, AVG(`TakenCourses`.grade) AS avg_grade
  FROM TakenCourses
  Students NATURAL JOIN takencourses
  GROUP BY `Students`.pNbr
  ORDER BY avg_grade DESC;
SELECT pNbr,MAX(avg_grade) from student_grade_average;

-- p
CREATE VIEW takencourses_view AS
  SELECT takencourses.pnbr, courses.credits
  FROM takencourses, courses
  WHERE takencourses.coursecode=courses.coursecode
  ORDER BY pnbr;
CREATE VIEW credit_view AS
  SELECT pnbr, SUM(credits) AS credits
  FROM takencourses_view
  GROUP BY pnbr;
SELECT students.pnbr, COALESCE(credit_view.credits, 0) AS credits
  FROM credit_view RIGHT OUTER JOIN Students
  ON credit_view.pnbr=students.pnbr
ORDER BY credits ASC;

-- q
SELECT s1.firstName, s1.lastName, s1.pNbr, COUNT(*) FROM Students as s1
INNER JOIN students ON s1.lastName = students.lastName AND s1.firstName = students.firstName
GROUP BY s1.pNbr
HAVING  COUNT(*) > 1;
