CREATE TABLE IF NOT EXISTS /*_*/user_google_user (
  mw_user_id int(10) UNSIGNED NOT NULL,
  user_email VARCHAR(320) NOT NULL,
  UNIQUE KEY mw_user_id_UNIQUE (mw_user_id),
  CONSTRAINT user_google_user_mw_user_id_fk FOREIGN KEY (mw_user_id) REFERENCES user (user_id) ON DELETE CASCADE
) /*$wgDBTableOptions*/;