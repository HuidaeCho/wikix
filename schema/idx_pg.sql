create index idx_admindb on admindb(id, pw);
create index idx_userdb on userdb(id, pw);
create index idx_page on page(id, version, hidden);
create index idx_data on data(id, version);
create index idx_linkfrom on link(linkfrom);
create index idx_linkto on link(linkto);
create index idx_linktoname on link(linktoname);
create index idx_taggedlinkfrom on taggedlink(linkfrom);
create index idx_taggedlinkto on taggedlink(linkto);
