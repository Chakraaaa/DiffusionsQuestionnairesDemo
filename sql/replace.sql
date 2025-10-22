SELECT * FROM `relais-managers`.quiz_question;

SELECT * FROM quiz_question WHERE label like "%\'font-size%"  ORDER BY ordre;
SELECT * FROM quiz_question WHERE label like "%Trebuchet MS'%"  ORDER BY ordre;
SELECT * FROM quiz_question WHERE label like "%font-weight: bold'%"  ORDER BY ordre;
SELECT * FROM quiz_question WHERE label_auto like "%\'font-size%"  ORDER BY ordre;
SELECT * FROM quiz_question WHERE label_auto like "%Trebuchet MS'%"  ORDER BY ordre;
SELECT * FROM quiz_question WHERE label_auto like "%font-weight: bold'%"  ORDER BY ordre;

SELECT * FROM template_quiz_question WHERE label like "%\'font-size%"  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label like '%''font-size%'  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label like "%Trebuchet MS'%"  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label like "%font-weight: bold'%"  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label_auto like "%\'font-size%"  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label_auto like "%Trebuchet MS'%"  ORDER BY ordre;
SELECT * FROM template_quiz_question WHERE label_auto like "%font-weight: bold'%"  ORDER BY ordre;

update quiz_question set label = REPLACE(label, '\'font-size', '"font-size') WHERE label like '%''font-size%';
update quiz_question set label = REPLACE(label, "Trebuchet MS'", "Trebuchet MS\"") WHERE label like "%Trebuchet MS'%";
update quiz_question set label = REPLACE(label, "font-weight: bold'", "font-weight: bold\"") WHERE label like "%font-weight: bold'%";
update quiz_question set label_auto = REPLACE(label, "'font-size", "\"font-size") WHERE label_auto like "%\'font-size%";
update quiz_question set label_auto = REPLACE(label, "Trebuchet MS'", "Trebuchet MS\"") WHERE label_auto like "%Trebuchet MS'%";
update quiz_question set label_auto = REPLACE(label, "font-weight: bold'", "font-weight: bold\"") WHERE label_auto like "%font-weight: bold'%";

update template_quiz_question set label = REPLACE(label, "'font-size", "\"font-size") WHERE label like "%\'font-size:%";
update template_quiz_question set label = REPLACE(label, "Trebuchet MS'", "Trebuchet MS\"") WHERE label like "%Trebuchet MS'%";
update template_quiz_question set label = REPLACE(label, "font-weight: bold'", "font-weight: bold\"") WHERE label like "%font-weight: bold'%";
update template_quiz_question set label_auto = REPLACE(label, "'font-size", "\"font-size") WHERE label_auto like "%\'font-size:%";
update template_quiz_question set label_auto = REPLACE(label, "Trebuchet MS'", "Trebuchet MS\"") WHERE label_auto like "%Trebuchet MS'%";
update template_quiz_question set label_auto = REPLACE(label, "font-weight: bold'", "font-weight: bold\"") WHERE label_auto like "%font-weight: bold'%";
