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