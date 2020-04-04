alter table types drop constraint types_uk1;
alter table types drop constraint types_uk2;
alter table types alter column icon drop not null;

alter table payment_methods drop constraint payment_methods_uk1;
alter table payment_methods drop constraint payment_methods_uk2;
alter table payment_methods alter column icon drop not null;
alter table payment_methods add unique (user_id, name);

alter table categories drop constraint categories_uk1;
alter table categories drop constraint categories_uk2;
alter table categories alter column icon drop not null;
alter table categories add unique (user_id, name);

update spends set type_id = 1 where type_id is null;
alter table spends alter column type_id set default 1;
alter table spends alter column type_id set not null;
