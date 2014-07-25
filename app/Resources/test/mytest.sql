use benassociation;
show tables;

-- insert into config(the_key, the_value) values('app_theme', 'theme1');

-- describe profile;
-- insert into fields value
-- 	(null, 'cotisation', 'id', 1, 1, 1, 'identifiant'),
-- 	(null, 'cotisation', 'user', 1, 1, 2, 'adherent'),
-- 	(null, 'cotisation', 'type', 1, 1, 3, 'montant'),
-- 	(null, 'cotisation', 'date_from', 1, 1, 4, 'date début'),
-- 	(null, 'cotisation', 'date_to', 1, 1, 5, 'date fin'),
-- 	(null, 'cotisation', 'description', 1, 1, 6, 'description')
-- ;

-- insert into fields value (null, 'adherent', 'etat', 1, 1, 4, 'Etat');
-- select * from config;
-- select username, enabled from user;

-- insert into fields value (null, 'adherant', 'groupList', 1, 1, 12, 'Groupe');
-- select * from fields;
-- select * from activity_log;
-- insert into config value (null, 'users_access', 1);
select * from config;
-- update config set the_key = 'allowaccess' where the_key = 'users_access';
