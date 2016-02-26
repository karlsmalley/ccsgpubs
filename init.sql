CREATE TABLE ccsg_users
(
  campuskey text NOT NULL,
  lastname text NOT NULL,
  firstname text NOT NULL,
  usertype text NOT NULL,
  user_id serial NOT NULL,
  email text,
  CONSTRAINT ccsg_users_pkey PRIMARY KEY (user_id)
)
WITH (
  OIDS=TRUE
);

CREATE TABLE ccsgpublications
(
  pubyear bigint,
  pubnum bigint,
  pubmedid bigint,
  disptext text,
  disppubdate text,
  pubdate date,
  journal text,
  authors text,
  authorsfull text,
  title text,
  volume text,
  issue text,
  pages text,
  sourcestring text,
  author_prog text,
  author_prog_prop_1 text,
  author_prog_prop_2 text,
  omitfrom text,
  report_year smallint,
  pmcid text,
  pubstatus text,
  journal_issn text,
  doi text,
  intraconsortium boolean,
  rowid serial NOT NULL,
  disptextbak text,
  notifyonupdate boolean,
  drexelpub boolean,
  limrpub boolean,
  afilliations text,
  orig_xml text,
  updated_xml text,
  pii text,
  journal_full_title text,
  CONSTRAINT ccsgpublications_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);

CREATE INDEX ccsglpubsjournal
  ON ccsgpublications
  USING btree
  (journal COLLATE pg_catalog."default");


CREATE INDEX ccsgoubsauthor
  ON ccsgpublications
  USING btree
  (author_prog COLLATE pg_catalog."default");


CREATE INDEX ccsgpubspubdate
  ON ccsgpublications
  USING btree
  (pubdate);


CREATE INDEX ccsgpubspubmedid_idx
  ON ccsgpublications
  USING btree
  (pubmedid);

 CREATE TABLE filters
(
  searchtext text,
  replacetext text,
  filter_when text,
  rowid serial NOT NULL,
  CONSTRAINT filters_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
); 

CREATE TABLE imp_factors
(
  rowid serial NOT NULL,
  journal_name text,
  issn text,
  impact_factor_13_14 numeric(12,6),
  impact_factor_12 numeric(12,6),
  impact_factor_11 numeric(12,6),
  impact_factor_10 numeric(12,6),
  impact_factor_9 numeric(12,6),
  impact_factor_8 numeric(12,6),
  abbrev_name text,
  CONSTRAINT imp_factors_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);

CREATE TABLE imp_fac_link
(
  publication_issn text,
  impact_factor_issn text,
  rowid serial NOT NULL,
  CONSTRAINT imp_fac_link_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);


CREATE INDEX imp_fac_link_impact_factor_issn_idx
  ON imp_fac_link
  USING btree
  (impact_factor_issn COLLATE pg_catalog."default");


CREATE INDEX imp_fac_link_pub_iss_idx
  ON imp_fac_link
  USING btree
  (publication_issn COLLATE pg_catalog."default");

CREATE TABLE members
(
  name text,
  mindate date,
  maxdate date,
  pmsearch text,
  pmsearch2 text,
  progs text,
  alt_progs_1 text,
  alt_progs_2 text,
  skipme boolean,
  isactive boolean,
  rowid serial NOT NULL,
  CONSTRAINT members_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);

CREATE TABLE programs
(
  program_name text,
  program_code text,
  iscurrprog boolean,
  isaltprog1 boolean,
  isaltprog2 boolean,
  isactive boolean,
  rowid serial NOT NULL,
  sortnum integer,
  CONSTRAINT programs_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);

 CREATE TABLE rejpubs
(
  pubmedid bigint,
  memname text,
  rowid serial NOT NULL,
  memberid bigint,
  CONSTRAINT rejpubs_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);

CREATE TABLE systemconf
(
  rowid integer NOT NULL,
  lastmod date,
  centername text,
  centerabbrev text,
  consortium boolean,
  historical_program_alignment text,
  proposed_alignment_1 text,
  proposed_alignment_2 text,
  CONSTRAINT systemconf_pkey PRIMARY KEY (rowid)
)
WITH (
  OIDS=TRUE
);
 
  INSERT INTO systemconf VALUES (1, '2016-02-26', 'Sidney Kimmel Cancer Center', 'SKCC', true, 'Historical Program Structure (publications listed with author''s program at the time of the publication)', '2017 Program Structure as of EAB Meeting on 11/6/2015.</b><br />All publications since 1/1/13 are listed with the authors 2017 Program Structure (as of 11/6/2015). <br />For former members, their publications (while they were members) are listed in their terminal program(s).', '2017 Program Structure (with CRC Program) as of EAB Meeting on 11/6/2015.</b><br />All publications since 1/1/13 are listed with the authors 2017 Program Structure (as of 11/6/2015). <br />For former members, their publications (while they were members) are listed in their terminal program(s).<b>');