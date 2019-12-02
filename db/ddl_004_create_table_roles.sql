create table roles
(
  id bigserial not null PRIMARY KEY,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  name varchar(20) not null UNIQUE
) without oids;
