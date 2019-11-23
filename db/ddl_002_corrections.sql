-- users
alter table users drop column image_url;
alter table users alter column first_name type varchar(100); 
alter table users alter column last_name type varchar(100); 
alter table users alter column email type varchar(100); 
alter table users alter column password_token type varchar(30);
alter table users add column photo varchar(100) UNIQUE;
alter table users add column active bool not null default true;

-- spends
alter table spends alter column amount type numeric(9, 2);
alter table spends alter column concept type varchar(100);
alter table spends alter column description type varchar(150);