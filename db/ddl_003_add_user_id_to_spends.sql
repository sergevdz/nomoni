-- spends
alter table spends add column user_id bigint;
alter table spends add foreign key (user_id) references users (id);
update spends set user_id = 1;
alter table spends alter column user_id set not null;

-- types
alter table types add column user_id bigint;
alter table types add foreign key (user_id) references users (id);
update types set user_id = 1;
alter table types alter column user_id set not null;

-- categories
alter table categories add column user_id bigint;
alter table categories add foreign key (user_id) references users (id);
update categories set user_id = 1;
alter table categories alter column user_id set not null;

-- payment_methods
alter table payment_methods add column user_id bigint;
alter table payment_methods add foreign key (user_id) references users (id);
update payment_methods set user_id = 1;
alter table payment_methods alter column user_id set not null;

