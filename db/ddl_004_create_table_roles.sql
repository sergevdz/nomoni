create table roles
(
  id bigserial not null primary key,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  name varchar(20) not null unique
) without oids;
