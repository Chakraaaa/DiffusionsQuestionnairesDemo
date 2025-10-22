DELETE FROM quiz_user_response WHERE quiz_user_id IN(SELECT id FROM quiz_user WHERE quiz_id = 6);
DELETE FROM quiz_user WHERE quiz_id = 6;
DELETE FROM quiz WHERE ID = 6;

