-- use benassociation;
-- show tables;

-- select DATE(created) as x ,sum(price) as y from cotisation group by x order by x;

-- select cin, cause, archived from profile;

-- SELECT  ROUND(DATEDIFF(Cast(NOW() as Date), Cast(birthday as Date)) / 365, 0) as age from profile;

-- select count(*) from profile where DATEDIFF(Cast(NOW() as Date), Cast(birthday as Date)) / 365 between 19 and 20 ;







use `membership`;
show tables;
select * from t_user;







-- select gender , count(*) as data from profile group by gender;

-- select created from user;


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
-- select * from config;
-- update config set the_key = 'allowaccess' where the_key = 'users_access';
-- insert into fields value (null, 'adherant', 'status', 1, 1, 13, 'Status');
-- select log.*, u.username from activity_log log
-- left join user u on u.id = log.user
-- where entity_id = 8;
-- update mygroup set kind = 'groupe de recherche' where 1=1;
-- describe mygroup;
-- select * from mygroup;
-- update profile set frequence = 1, montant = 1000 where 1=1;
-- select frequence, montant from profile;
-- select now();
-- select max(date_to), DATEDIFF(max(date_to) , now()) from cotisation where user_id = 18;

-- select user_id id, DATEDIFF(CURRENT_DATE(), max(date_to)) days from cotisation group by user_id;
-- select * from cotisation group by user_id having DATEDIFF(max(date_to) , now()) < 0;
-- SELECT u.username, c.* FROM user u
-- LEFT JOIN profile p on p.id = u.profile_id
-- LEFT JOIN user_group gu on gu.user_id = u.id
-- LEFT JOIN mygroup g on g.id = gu.group_id
-- LEFT JOIN avancement av on av.user_id = u.id
-- LEFT JOIN status s on s.id = av.status_id
-- LEFT JOIN cotisation c on c.user_id = u.id 
-- where u.id in (select id from (select user_id id, DATEDIFF(max(date_to), CURRENT_DATE()) days from cotisation group by user_id)A where A.days < 0)
-- GROUP BY u.id
-- HAVING DATEDIFF(max(c.date_to), CURRENT_DATE()) < 0
-- ;
-- select id from (select user_id as id, DATEDIFF(max(date_to), CURRENT_DATE()) days from cotisation group by user_id having days < 0 )A ;
-- select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id  group by user_id having DATEDIFF(max(c.date_to), CURRENT_DATE()) >= 0 ; 


-- select user_id as id, DATEDIFF(max(date_to), CURRENT_DATE()) days from cotisation group by user_id having days < 0 ;
-- select count(*) as id from cotisation c having  DATEDIFF(max(c.date_to), CURRENT_DATE()) >= 0 ;
-- select count(*) as ids from cotisation c having  DATEDIFF(max(c.date_to), CURRENT_DATE()) < 0 ;
-- select count(*) from user;
-- select count(*) from cotisation group by user_id;

-- select count(*) from user;
-- select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id 
-- where u.id in (select id from (select user_id id, DATEDIFF(max(date_to), CURRENT_DATE()) days from cotisation group by user_id)A where A.days < 0)

-- select * from 
-- (select count(*) as yes from (select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id  group by user_id having DATEDIFF(max(c.date_to), CURRENT_DATE()) >= 0) A) A,
-- (select count(*) as no from (select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id  group by user_id having DATEDIFF(max(c.date_to), CURRENT_DATE()) < 0) A) B,
-- (select count(*) as never from user u left join cotisation c on c.user_id = u.id where c.id is NULL) C;

